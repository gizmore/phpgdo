<?php
namespace GDO\Install;

use Exception;
use GDO\Core\Method;
use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Core\GDO;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;
use GDO\User\GDO_Permission;
use GDO\Util\Strings;
use GDO\Core\Application;
use GDO\Install\Method\Configure;
use GDO\Core\ModuleProviders;
use GDO\DBMS\Module_DBMS;

/**
 * Install helper.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class Installer
{
	/**
	 * @param GDO_Module[] $modules
	 */
	public static function installModules(array $modules, bool $forceMigrate=false) : bool
	{
		$app = Application::$INSTANCE;
		$isCLI = $app->isCLI();
		$isTest = $app->isUnitTests();
		$isInstall = $app->isInstall();
		
		if ($isInstall && $isCLI)
		{
			echo "Checking system dependencies...\n";
		}
		
		$passed = true;
		foreach ($modules as $module)
		{
			if (!$module->checkSystemDependencies())
			{
				$passed = false;
			}
		}
		
		if (!$passed)
		{
			return false;
		}
		
		usort($modules, function(GDO_Module $a, GDO_Module $b) {
			return $a->priority - $b->priority;
		});
		
		/**
		 * @var $module GDO_Module
		 */
		foreach ($modules as $module)
		{
			if (!$module->isInstallable())
			{
				continue;
			}
			if ( ($isInstall && $isCLI) || ($isTest) )
			{
				echo "Installing {$module->getName()}\n";
			}
			self::installModule($module, $forceMigrate);
		}
		return true;
	}
	
	public static function installModuleWithDependencies(GDO_Module $module, bool $forceMigrate=false) : void
	{
		$modules = self::getDependencyModules($module->getName());
		$modules[] = $module;
		self::installModules($modules, $forceMigrate);
	}
	
	public static function installModule(GDO_Module $module, bool $forceMigrate=false) : void
	{
		self::installModuleClasses($module, $forceMigrate);
		
		if (!$module->isPersisted())
		{
			$module->setVars([
				'module_name' => $module->getModuleName(),
				'module_enabled' => '1',
				'module_version' => $module->version,
				'module_priority' => $module->priority,
			])->insert();
		}
		else
		{
			ModuleLoader::instance()->setModule($module);
		}
		
		$upgraded = false;
		while ($module->getVersion()->__toString() !== $module->version)
		{
			self::upgrade($module);
			$upgraded = true;
		}
		
		if ($forceMigrate || $upgraded)
		{
			self::recreateDatabaseSchema($module);
		}
		
		self::installMethods($module);

		$module->onInstall();
		
		ModuleLoader::instance()->addEnabledModule($module);
	}
	
	public static function installModuleClasses(GDO_Module $module) : void
	{
		if ($classes = $module->getClasses())
		{
			foreach ($classes as $class)
			{
				if (is_subclass_of($class, 'GDO\Core\GDO'))
				{
					$gdo = $class::table();
					$gdo instanceof GDO;
					if (!$gdo->gdoAbstract())
					{
						self::installModuleClass($gdo);
					}
				}
			}
		}
	}
	
	public static function installModuleClass(GDO $gdo) : void
	{
		$gdo->createTable();
	}
	
	public static function dropModule(GDO_Module $module) : void
	{
		$db = Database::instance();
		try
		{
			$db->disableForeignKeyCheck();
			$module->onWipe();
			self::dropModuleClasses($module);
			$module->delete();
			Cache::remove('gdo_modules');
		}
		catch (Exception $ex)
		{
			throw $ex;
		}
		finally
		{
			$db->enableForeignKeyCheck();
		}
	}

	public static function dropModuleClasses(GDO_Module $module) : void
	{
		if ($classes = $module->getClasses())
		{
			foreach (array_reverse($classes) as $class)
			{
				if (is_subclass_of($class, 'GDO\\Core\\GDO'))
				{
					$gdo = $class::table();
					/** @var $gdo GDO **/
					if (!$gdo->gdoAbstract())
					{
						$gdo->dropTable();
					}
				}
			}
		}
	}
	
	public static function upgrade(GDO_Module $module) : void
	{
		$version = self::increaseVersion($module, false);
		self::upgradeTo($module, $version);
		self::increaseVersion($module, true);
	}
		
	/**
	 * On an upgrade we execute a possible upgrade file.
	 * We also recreate the database schema.
	 */
	public static function upgradeTo(GDO_Module $module, string $version) : void
	{
		self::includeUpgradeFile($module, $version);
		self::recreateDatabaseSchema($module, $version);
	}
	
	/**
	 * Recreate a database schema.
	 * I call this "automigration".
	 * COPY table. DROP table. CREATE table. RE-IMPORT table. Works :)
	 * 
	 * @version 7.0.1
	 * @since 6.11.5
	 */
	public static function recreateDatabaseSchema(GDO_Module $module) : void
	{
		if ($classes = $module->getClasses())
		{
			try
			{
				$db = Database::instance();
				foreach ($classes as $classname)
				{
					/**
					 * @var GDO $gdo
					 */
					$gdo = $classname::table();
					if ($gdo->gdoIsTable())
					{
						Database::DBMS()->dbmsAutoMigrate($gdo);
					}
				}
			}
			catch (\Throwable $t)
			{
				throw $t;
			}
			finally
			{
				$db->enableForeignKeyCheck();
			}
		}
	}
	
// 	/**
// 	 * Get intersecting columns of old and new table creatoin schema.
// 	 * @return string[]
// 	 */
// 	private static function getColumnNames(GDO $gdo, string $temptable) : array
// 	{
// 	}
	
	public static function includeUpgradeFile(GDO_Module $module, string $version) : void
	{
		$upgradeFile = $module->filePath("upgrade/$version.php");
		if (FileUtil::isFile($upgradeFile))
		{
			include($upgradeFile);
		}
	}
	
	/**
	 * Increase version by one patch level.
	 */
	private static function increaseVersion(GDO_Module $module, bool $write) : string
	{
		$version = $module->getVersion();
		$version->increase();
		$v = $version->__toString();
		if ($write)
		{
			$module->saveVar('module_version', $v);
		}
		return $v;
	}
	
	public static function installMethods(GDO_Module $module)
	{
		self::loopMethods($module, array(__CLASS__, 'installMethod'));
	}
	
	public static function loopMethods(GDO_Module $module, $callback)
	{
		$dir = $module->filePath('Method');
		if (FileUtil::isDir($dir))
		{
			Filewalker::traverse($dir, null, $callback, null, 0, $module);
		}
	}
	
	/**
	 * Helper to get the method for a method loop.
	 * @param GDO_Module $module
	 * @param string $path
	 * @return Method
	 * @deprecated because the naming is horrible. Also the logic is not nice.
	 */
	public static function loopMethod(GDO_Module $module, $path)
	{
		$entry = Strings::substrTo(basename($path), '.');
		$class_name = "GDO\\{$module->getName()}\\Method\\$entry";
		if (!class_exists($class_name, false))
		{
			include $path;
		}
		return $module->getMethod($entry);
	}
	
	public static function installMethod($entry, $path, GDO_Module $module)
	{
		$method = self::loopMethod($module, $path);
		if ($permissions = $method->getPermission())
		{
			foreach (explode(',', $permissions) as $permission)
			{
				GDO_Permission::create($permission);
			}
		}
	}
	
	public static function getDependencyModules(string $moduleName, bool $friendencies=false) : array
	{
		return array_map(function(string $moduleName) {
			return ModuleLoader::instance()->getModule($moduleName, true);
		}, self::getDependencyModuleNames($moduleName));
	}
	
	public static function getFriendencyModules(string $moduleName) : array
	{
		return self::getDependencyModuleNames($moduleName, true);
	}
	
	/**
	 * Return all modules needed for a module.
	 * Used in phpgdo-docs to generate a module list for a single module documentation output.
	 * @return GDO_Module[]
	 */
	public static function getDependencyModuleNames(string $moduleName, bool $friendencies=false, bool $noCore=true) : array
	{
	    $module = ModuleLoader::instance()->loadModuleFS($moduleName, true, false);
	    $moduleName = $module->getModuleName();
	    $deps = $module->getDependencies();
	    $frds = $module->getFriendencies();
// 	    $frds[] = $module->getModuleName();
	    $deps[] = $module->getModuleName();
	    $deps[] = 'Core';
	    $cnt = 0;
	    while ($cnt !== count($deps))
	    {
	        $cnt = count($deps);
	        foreach ($deps as $dep)
	        {
	            $depmod = ModuleLoader::instance()->loadModuleFS($dep, true, false);
	            if (!$depmod)
	            {
	                continue;
	            }
	            $moreFrds = $depmod->getFriendencies();
	            $moreDeps = $depmod->getDependencies();
	            $frds = array_unique(array_merge($frds, $moreFrds));
	            $deps = array_unique(array_merge($deps, $moreDeps));
	        }
	    }
	    
	    $frds = array_diff($frds, $deps);
	    
	    $deps = $friendencies ? $frds : $deps;
	    
	    if ($noCore)
	    {
		    $deps = array_filter($deps,
		    	function (string $name) use ($moduleName)
		    	{
		    		if (ModuleProviders::isCoreModule($name))
		    		{
		    			return false;
		    		}
		    		return $name !== $moduleName;
		    });
	    }
	    
	    sort($deps);
	    
	    return $deps;
	}
	
	########################
	### Config Refresher ###
	########################
	/**
	 * In case a new config.php variable is introduced, this method can upgrade your config.
	 */
	public static function refreshConfig(string $path) : bool
	{
		copy($path, $path . '.backup.php');
		Config::configure();
		Configure::make()->writeConfig($path);
		return true;
	}
	
	###################
	### Module Info ###
	###################
	/**
	 * Module description is fetched from README.md by default. Additionally, all Method's phpdoc is read.
	 */
	public static function getModuleDescription(GDO_Module $module): string
	{
		$back = '';
		if ($readme = @file_get_contents($module->filePath('README.md')))
		{
			$matches = null;
			if (preg_match("/^#.*[\\r\\n]+([^#]+)/", $readme, $matches))
			{
				$back .= trim($matches[1])."<br/>\n<br/>\n";
			}
		}
		
		$back .= self::getClassDescription($module);
		
		foreach ($module->getClasses() as $klass)
		{
			$back .= self::getClassNameDescription($klass);
		}
		
		$back .= "\n";
		
		foreach ($module->getMethods(false) as $method)
		{
			$back .= self::getClassDescription($method);
		}
		
		return trim($back);
	}
	
	/**
	 * Get a class' phpdoc description.
	 */
	public static function getClassDescription(object $object) : ?string
	{
		$klass = get_class($object);
		return self::getClassNameDescription($klass);
	}
	
	/**
	 * Get a class' phpdoc description.
	 */
	public static function getClassNameDescription(string $klass) : ?string
	{
		$klass = str_replace('\\', '/', $klass);
		$filename = GDO_PATH . $klass . '.php';
		if ($sourcecode = @file_get_contents($filename))
		{
			$matches = null;
			if (preg_match_all("/[\r\n]\/\*\*[\s\*\r\n]*([\.\s\w]+)/", $sourcecode, $matches))
			{
				return trim($matches[1][0])."<br/>\n";
			}
		}
		return null;
	}
	
}
