<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Util\Filewalker;
use GDO\Util\FileUtil;
use GDO\Language\Trans;

/**
 * Module loader.
 * Can load from DB and/or FS.
 * Uses memcached for fast modulecache loading.
 *
 * @author gizmore
 * @version 7.0.2
 * @since 3.0.0
 */
final class ModuleLoader
{
	private static self $INSTANCE;

	public static function instance() : self { return self::$INSTANCE; }
	
	/**
	 * Base modules path, the modules folder.
	 */
	private string $path;
	
	public function __construct(string $path)
	{
		self::$INSTANCE = $this;
		$this->path = $path;
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
	 * @var \SplFixedArray[GDO_Module]
	 */
	private array $enabledModules;
	
	/**
	 * Get all enabled and loaded modules.
	 * @return GDO_Module[]
	 */
	public function &getEnabledModules(): array
	{
	    if (!isset($this->enabledModules))
	    {
	    	# Filter
	    	$this->enabledModules = array_filter($this->modules, function(GDO_Module $module) {
    			return $module->isEnabled();
    		});
	    }
	    return $this->enabledModules;
	}
	
	public function addEnabledModule(GDO_Module $module) : void
	{
		if (!in_array($module, $this->getEnabledModules(), true))
		{
			$this->initModuleVars($module->getModuleName());
			$module->initOnce();
			$this->enabledModules[] = $module;
		}
	}
	
	public function flushEnabledModules() : void
	{
		unset($this->enabledModules);
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
	
	public function setModule(GDO_Module $module)
	{
		$name = strtolower($module->getName());
		$this->modules[$name] = $module;
	}
	
	public function getModule(string $moduleName, bool $fs = false, bool $throw = true) : ?GDO_Module
	{
		$caseName = $moduleName;
	    $moduleName = strtolower($moduleName);
	    if (isset($this->modules[$moduleName]))
	    {
	        return $this->modules[$moduleName];
	    }
	    if ($fs)
	    {
	    	return $this->loadModuleFS($caseName, $throw);
	    }
	    if ($throw)
	    {
	    	throw new GDO_Error('err_module', [html($caseName)]);
	    }
	    return null;
	}
	
	/**
	 * Get a module by ID.
	 * @return GDO_Module
	 */
	public function getModuleByID(string $moduleID) : ?GDO_Module
	{
		foreach ($this->modules as $module)
		{
			if ($module->getID() === $moduleID)
			{
				return $module;
			}
		}
		return null;
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
		if (null === ($cache = Cache::get('gdo_modules')))
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
	}
	
	public function loadLangFiles(bool $all=false) : void
	{
		Trans::inited(false);
		$modules = $all ? $this->modules : $this->getEnabledModules();
		foreach ($modules as $module)
		{
	        $module->onLoadLanguage();
		}
		Trans::inited(true);
	}
	
	public function initModules() : void
	{
		# Register themes and load language
		foreach ($this->getEnabledModules() as $module)
		{
	        if ($theme = $module->getTheme())
	        {
	            GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
	        }
		}
		$this->initModulesB();
	}
		
	private function initModulesB() : void
	{
		# Init modules
		$app = Application::$INSTANCE;
		if (!$app->isInstall())
		{
			foreach ($this->getEnabledModules() as $module)
			{
				$module->initOnce();
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
			$this->reset();
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
			$this->loadModulesFS(false);
			$loaded = $this->loadedFS = true;
		}
		
		# Loaded one?
		if ($loaded)
		{
			$order = 'module_priority ASC, module_name ASC';
			$this->modules = $this->sortModules($order);
		}
		return $this->modules;
	}
	
	/**
	 * Force module reloading.
	 */
	public function reset() : self
	{
		$this->loadedDB = false;
		$this->loadedFS = false;
		$this->modules = [];
		unset($this->enabledModules);
		return $this;
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
				else
				{
					$module = $this->modules[$moduleName];
					$module->setVars($moduleData, false);
					$module->setPersisted(true);
				}
			}
			return $this->modules;
		}
		catch (\GDO\Core\GDO_DBException $e)
		{
		    if (Application::$INSTANCE->isCLI())
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
		$this->sortModules('module_priority ASC');
		if ($init)
		{
			foreach ($this->modules as $module)
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
			if (!str_starts_with($entry, 'phpgdo-'))
			{
				$this->loadModuleFS($entry, true, $init);
			}
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
// 			try
// 			{
				if (@class_exists($className, true))
				{
					$moduleData = GDO_Module::table()->getBlankData(['module_name' => $name]);
					if ($module = self::instanciate($moduleData, true))
					{
						$this->modules[$lowerName] = $module;
// 					    $module->onLoadLanguage();
// 					    if ($theme = $module->getTheme())
// 					    {
// 					        GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
// 					    }
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
// 			}
// 			catch (\Throwable $t)
// 			{
// 				return null;
// 			}
		}
		if ($init)
		{
			$module = $this->modules[$lowerName];
// 			$module->buildConfigCache();
// 			$this->initModuleVars($module->getName());
			$module->onModuleInit();
		}
		return $this->modules[$lowerName];
	}
	
	/**
	 * @var GDO_Module[]
	 */
	private static array $INSTANCES = [];
	
	/**
	 * Instanciate a module from gdoVars/loaded data.
	 */
	private static function instanciate(array $moduleData, bool $dirty = false) : GDO_Module
	{
		$name = $moduleData['module_name'];
		$klass = "GDO\\$name\\Module_$name";
		/** @var $instance GDO_Module **/
		if (class_exists($klass))
		{
			if (isset(self::$INSTANCES[$name]))
			{
				$instance = self::$INSTANCES[$name];
			}
			else
			{
				$instance = self::$INSTANCES[$name] = new $klass();
			}
    		$moduleData['module_priority'] = $instance->priority;
    		$moduleData['module_enabled'] = $dirty ? '0' : $moduleData['module_enabled'];
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
	public function initModuleVars()
	{
// 	    foreach ($this->getEnabledModules() as $module)
// 	    {
// 	        $module->buildConfigCache();
// 	    }
	    
		# Query all module vars
		try
		{
		    if (GDO_DB_ENABLED)
		    {
		    	if (!($data = Cache::fileGetSerialized('gdo_modulevars')))
		    	{
		    		$query = GDO_ModuleVar::table()->select('mv_module, mv_name, mv_value');
// 		    		if ($singleModuleName)
// 		    		{
// 		    			$query->where('module_name='.quote($singleModuleName));
// 		    		}
		    		$data = $query->exec()->fetchAllRows();
// 		    		if (!$singleModuleName)
// 		    		{
		    			Cache::fileSetSerialized('gdo_modulevars', $data);
// 		    		}
		    	}
		    	
        		# Assign them to the modules
		    	foreach ($data as $row)
		    	{
// 		    		if ())
// 		    		{
		    			$module = $this->getModuleByID($row[0]);
		    			$module->addConfigVarForCache($row[1], $row[2]);
// 		    		}
//         		while ($row = $result->fetchRow())
//         		{
        		    /** @var $module \GDO\Core\GDO_Module **/
//         			if ($module = @$this->modules[strtolower($row[0])])
//         			{
//         				if ($gdt = $module->getConfigColumn($row[1], false))
//         				{
//         				    $gdt->initial($row[2]);
//         				}
//         			}
        		}
		    }
		}
		catch (\GDO\Core\GDO_DBException $ex)
		{
			$app = Application::$INSTANCE;
		    if ($app->isCLI()) # && (!$app->isInstall()))
		    {
		    	if (!$app->isUnitTests())
		    	{
		    		echo "No database available yet...\n";
		    	}
		    	else
		    	{
		    		Application::$RESPONSE_CODE = 200;
		    	}
		    }
		}
		catch (\Throwable $e)
		{
		    Logger::logException($e);
		}
		
// 		foreach ($this->getEnabledModules() as $module)
// 		{
//     		$module->buildSettingsCache();
// 		}
	}
	
	public function sortModules(string $orders) : array
	{
		uasort($this->modules, function(GDO_Module $a, GDO_Module $b) {
			return $a->priority - $b->priority;
		});
	    return $this->modules;
	}
	
}
