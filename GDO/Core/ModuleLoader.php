<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\File\Filewalker;
use GDO\Language\Trans;
use GDO\DB\Database;
use GDO\Table\Sort;
use GDO\CLI\CLIUtil;

/**
 * Module loader.
 * Can load from DB and/or FS.
 * Uses memcached for fast modulecache loading.
 *
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.0
 */
final class ModuleLoader
{
	/**
	 * @return ModuleLoader
	 */
	public static function instance() : self { return self::$INSTANCE; }
	private static ModuleLoader $INSTANCE;
	
	/**
	 * Base modules path, the modules folder.
	 * @var string
	 */
	private string $path;
	public function __construct(string $path)
	{
		$this->path = $path;
		self::$INSTANCE = $this;
	}
	
	#############
	### Cache ###
	#############
	/**
	 * @var GDO_Module[]
	 */
	private array $modules = [];
	
	/**
	 * Get all loaded modules.
	 * @return GDO_Module[]
	 */
	public function &getModules() : array
	{
		return $this->modules;
	}
	
	/**
	 * @var GDO_Module[]
	 */
	public static ?array $ENABLED_MODULES = null;
	
	/**
	 * Get all enabled and loaded modules.
	 * @return GDO_Module[]
	 */
	public function getEnabledModules() : array
	{
	    if (self::$ENABLED_MODULES === null)
	    {
	        $enabled = array_filter($this->modules, function(GDO_Module $module) {
    			return $module->isEnabled();
    		});
	        self::$ENABLED_MODULES = &$enabled;
	    }
	    return self::$ENABLED_MODULES;
	}
	
	/**
	 * Get all enabled and loaded modules.
	 * @return GDO_Module[]
	 */
	public function getInstallableModules() : array
	{
		return array_filter($this->modules, function(GDO_Module $module){
			return $module->isInstallable();
		});
	}
	
	public function getModule(string $moduleName, bool $fs = false, bool $throw = true) : ?GDO_Module
	{
	    $moduleName = strtolower($moduleName);
	    if (isset($this->modules[$moduleName]))
	    {
	        return $this->modules[$moduleName];
	    }
	    if ($fs)
	    {
	    	return $this->loadModuleFS($moduleName, $throw);
	    }
	    if ($throw)
	    {
	    	throw new GDO_Error('err_module', [html($moduleName)]);
	    }
	    return null;
	}
	
	/**
	 * Get a module by ID.
	 * @return GDO_Module
	 */
	public function getModuleByID(string $moduleID) : GDO_Module
	{
		foreach ($this->modules as $module)
		{
			if ($module->getID() === $moduleID)
			{
				return $module;
			}
		}
	}
	
	#################
	### Cacheload ###
	#################
	/**
	 * Load active modules, preferably from cache.
	 * Sorted by priority to be spinlock free.
	 * @return GDO_Module[]
	 */
	public function loadModulesCache() : array
	{
		if (false === ($cache = Cache::get('gdo_modules')))
		{
			$cache = $this->loadModulesA();
			Cache::set('gdo_modules', $cache);
		}
		else
		{
			$this->initFromCache($cache);
		}
		return $this->modules;
	}
	
	private function initFromCache(array $cache) : void
	{
		$this->modules = $cache;
		$this->initModules();
	}
	
	public function initModules() : void
	{
		# Register themes and load language
		foreach ($this->getEnabledModules() as $module)
		{
	        $module->onLoadLanguage();
	        if ($theme = $module->getTheme())
	        {
	            GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
	        }
		}
		Trans::inited(true);
	}
		
	public function initModulesB() : void
	{
		# Init modules
		$app = Application::instance();
		if (!$app->isInstall())
		{
			foreach ($this->getEnabledModules() as $module)
			{
				if (!$module->isInited())
				{
					$module->onInit();
					if (CLIUtil::isCLI())
					{
						$module->onInitCLI();
					}
					$module->initedModule();
				}
			}
		}
	}
	
	private bool $scriptsIncluded = false;
	public function onIncludeScripts() : void
	{
		if (!$this->scriptsIncluded)
		{
			$this->scriptsIncluded = true;
			foreach ($this->getEnabledModules() as $module)
			{
				$module->onIncludeScripts();
			}
		}
	}
	
	##################
	### Massloader ###
	##################
	private bool $loadedDB = false;
	private bool $loadedFS = false;
	
	/**
	 * @return GDO_Module[]
	 */
	public function loadModulesA() : array
	{
		return $this->loadModules(!!GDO_DB_ENABLED, !GDO_DB_ENABLED);
	}
	
	/**
	 * Load all modules.
	 * @return GDO_Module[]
	 */
	public function loadModules(bool $loadDB=true, bool $loadFS=false, bool $refresh=false) : array
	{
		if ($refresh)
		{
		    $this->loadedDB = false;
		    $this->loadedFS = false;
			$this->modules = [];
		}
		
		# Load maybe 0, 1 or 2 sources
		$loaded = false;
		if ($loadDB && (!$this->loadedDB) )
		{
			$this->loadedDB = $this->loadModulesDB() !== false;
			$loaded = true;
		}
		
		if ($loadFS && (!$this->loadedFS) )
		{
// 		    $init = !$loadDB;
			$this->loadModulesFS(false);
			$loaded = $this->loadedFS = true;
		}
		
		# Loaded one?
		if ($loaded)
		{
// 		    if ( (!Application::instance()->isInstall()) ||
// 		         ($this->loadedDB) )
// 		    {
    			$this->initModuleVars();
// 		    }

			$this->modules = $this->sortModules([
			    'module_priority' => true,
				'module_name' => true
			]);
			
			$this->initModules();
		}
		return $this->modules;
	}
	
	private function loadModulesDB()
	{
	    if (!GDO_DB_ENABLED)
	    {
	        return false;
	    }
		try
		{
			$result = GDO_Module::table()->select()->exec();
			while ($moduleData = $result->fetchAssoc())
			{
				$moduleName = strtolower($moduleData['module_name']);
				if (!isset($this->modules[$moduleName]))
				{
					try
					{
						if ($module = self::instanciate($moduleData))
						{
							$this->modules[$moduleName] = $module->setPersisted(true);
						}
					}
					catch (\Throwable $e)
					{
						echo "A module file or folder is missing in filesystem: GDO/{$moduleName}(\n";
					}
				}
			}
			return $this->modules;
		}
		catch (\GDO\Core\GDO_DBException $e)
		{
		    if (Application::instance()->isCLI())
		    {
    		    echo "The table gdo_module does not exist yet.\n";
				echo "You can ignore this error if you are using the CLI installer.\n";
		    }
		    return false;
		}
		catch (\Throwable $e)
		{
		    Logger::logException($e);
			return false;
		}
	}
	
	private function loadModulesFS(bool $init=true) : void
	{
// 	    Trans::inited(false);
		Filewalker::traverse($this->path, null, null, [$this, '_loadModuleFS'], 0, $init);
		Trans::inited(true);
		$this->sortModules(['module_priority' => true]);
		foreach ($this->modules as $module)
		{
			if ($init)
			{
				$module->buildConfigCache();
				$module->buildSettingsCache();
			}
		}
	}
	
	public function _loadModuleFS(string $entry, string $path, bool $init)
	{
		if (FileUtil::isFile("$path/Module_$entry.php"))
		{
			$this->loadModuleFS($entry, true, $init);
		}
	}
	
	/**
	 * Load a module from filesystem if it is not loaded yet.
	 * @param string $name The case sensitive name.
	 * @param boolean $throw If it shall throw an exception if not found.
	 * @return \GDO\Core\GDO_Module
	 */
	public function loadModuleFS(string $name, bool $throw=true, bool $init=false)
	{
	    $lowerName = strtolower($name);
	    
		if (!isset($this->modules[$lowerName]))
		{
			$className = "GDO\\$name\\Module_$name";
			if (@class_exists($className, true))
			{
				$moduleData = GDO_Module::table()->getBlankData(['module_name' => $name]);
				if ($module = self::instanciate($moduleData, true))
				{
					$this->modules[$lowerName] = $module;
				    $module->onLoadLanguage();
				    if ($theme = $module->getTheme())
				    {
				        GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
				    }
				}
			}
			elseif ($throw)
			{
				throw new GDO_Error('err_module', [html($name)]);
			}
			else
			{
			    return null;
			}
		}
		if ($init)
		{
			$module = $this->modules[$lowerName];
			$module->buildConfigCache();
			$this->initModuleVars($module->getName());
		}
		return $this->modules[$lowerName];
	}
	
	/**
	 * Instanciate a module from gdoVars/loaded data.
	 * @param array $moduleData
	 * @param bool $dirty
	 * @throws GDO_Error
	 * @return \GDO\Core\GDO_Module
	 */
	public static function instanciate(array $moduleData, bool $dirty = false) : GDO_Module
	{
		$name = $moduleData['module_name'];
		$klass = "GDO\\$name\\Module_$name";
		/** @var $instance GDO_Module **/
		if (class_exists($klass))
		{
    		$instance = new $klass();
    		$instance->isTable = false;
    		$moduleData['module_priority'] = $instance->priority;
    		$instance->setGDOVars($moduleData, $dirty);
    		return $instance;
		}
	}
	
	############
	### Vars ###
	############
	/**
	 * Load module vars from database.
	 */
	public function initModuleVars(string $singleModuleName=null)
	{
	    foreach ($this->modules as $module)
	    {
	        $module->buildConfigCache();
	    }
	    
		# Query all module vars
		try
		{
		    if (Database::instance())
		    {
    		    $query = GDO_ModuleVar::table()->select('module_name, mv_name, mv_value')->joinObject('mv_module');
        		if ($singleModuleName)
        		{
        			$query->where('module_name='.quote($singleModuleName));
        		}
    		    $result = $query->exec();
    		    
        		# Assign them to the modules
        		while ($row = $result->fetchRow())
        		{
        		    /** @var $module \GDO\Core\GDO_Module **/
        			if ($module = @$this->modules[strtolower($row[0])])
        			{
        				if ($gdt = $module->getConfigColumn($row[1], false))
        				{
        				    $gdt->initial($row[2]);
        				}
        			}
        		}
		    }
		}
		catch (\GDO\Core\GDO_DBException $e)
		{
			$app = Application::instance();
		    if ($app->isCLI()) # && (!$app->isInstall()))
		    {
		        echo "No database available yet...\n";
		    }
		}
		catch (\Throwable $e)
		{
		    Logger::logException($e);
		}
		
		foreach ($this->modules as $module)
		{
    		$module->buildSettingsCache();
		}
	}
	
	public function sortModules(array $orders)
	{
	    Sort::sortArray($this->modules, GDO_Module::table(), $orders);
	    return $this->modules;
	}
	
}
