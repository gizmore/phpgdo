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
use GDO\Core\Debug;

/**
 * Install helper.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class Installer
{
	/**
	 * @param GDO_Module[] $modules
	 */
	public static function installModules(array $modules) : bool
	{
		$isCLI = Application::$INSTANCE->isCLI();
		$isInstall = Application::$INSTANCE->isInstall();
		
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
		
		/**
		 * @var $module GDO_Module
		 */
		foreach ($modules as $module)
		{
			if (!$module->isInstallable())
			{
				continue;
			}
			try
			{
				if ($isInstall && $isCLI)
				{
					echo "Installing {$module->getName()}\n";
					flush();
				}
				self::installModule($module);
			}
			catch (\Throwable $e)
			{
				$app = Application::$INSTANCE;
				if ($app->isCLI())
				{
					echo Debug::backtraceException($e, false, "Cannot install {$module->getName()}");
				}
				else
				{
					throw $e;
				}
			}
		}
		return true;
	}
	
	public static function installModuleWithDependencies(GDO_Module $module) : void
	{
		$modules = self::getDependencyModules($module->getName());
		self::installModules($modules);
	}
	
	public static function installModule(GDO_Module $module, bool $forceMigrate=false) : void
	{
		self::installModuleClasses($module, $forceMigrate);
		
		if (!$module->isPersisted())
		{
			$version = $module->version;
			$module->setVars([
				'module_name' => $module->getName(),
				'module_enabled' => '1',
				'module_version' => $version,
				'module_priority' => $module->priority,
			]);
			$module->insert();
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
						# Remove old temp table
						$tablename = $gdo->gdoTableName();
						$temptable = "zzz_temp_{$tablename}";
						$db->dropTableName($temptable);
						
						# create temp and copy as old
						$db->disableForeignKeyCheck();
						# Do not! drop the temp table. It might contain live data from a failed upgrade
						$query = "SHOW CREATE TABLE $tablename";
						$result = Database::instance()->queryRead($query);
						$query = mysqli_fetch_row($result)[1];
						$query = str_replace($tablename, $temptable, $query);
						$db->queryWrite($query);
						$query = "INSERT INTO $temptable SELECT * FROM $tablename";
						$db->queryWrite($query);

						# drop existing and recreate as new
						$query = "DROP TABLE $tablename";
						$db->queryWrite($query);
						$gdo->createTable(); # CREATE TABLE IF NOT EXIST
						$db->disableForeignKeyCheck();

						# calculate columns and copy back in new
						if ($columns = self::getColumnNames($gdo, $temptable))
						{
							$columns = implode(',', $columns);
							$query = "INSERT INTO $tablename ($columns) SELECT $columns FROM $temptable";
							$db->queryWrite($query);
							
							# drop temp after all succeded.
							$query = "DROP TABLE $temptable";
							$db->queryWrite($query);
						}
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
	
	/**
	 * Get intersecting columns of old and new table creatoin schema.
	 * 
	 * @return string[]
	 */
	private static function getColumnNames(GDO $gdo, string $temptable) : array
	{
		$db = GDO_DB_NAME;
		
		$query = "SELECT group_concat(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS " .
		         "WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = '{$temptable}'";
		$result = Database::instance()->queryRead($query);
		$old = mysqli_fetch_array($result)[0];
		$old = explode(',', $old);
		
		$query = "SELECT group_concat(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS " .
		"WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = '{$gdo->gdoTableName()}'";
		$result = Database::instance()->queryRead($query);
		$new = mysqli_fetch_array($result)[0];
		$new = explode(',', $new);
		
		return ($old && $new) ? 
			array_intersect($old, $new) : [];
	}
	
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
		if ($permission = $method->getPermission())
		{
			GDO_Permission::create($permission);
		}
	}

	/**
	 * Return all modules needed for a module.
	 * Used in phpgdo-docs to generate a module list for a single module documentation output.
	 * @return GDO_Module[]
	 */
	public static function getDependencyModules(string $moduleName) : array
	{
	    $module = ModuleLoader::instance()->loadModuleFS($moduleName, false, true);
	    $deps = $module->getDependencies();
	    $deps[] = $module->getName();
	    $deps[] = 'Core';
	    $cnt = 0;
	    while ($cnt !== count($deps))
	    {
	        $cnt = count($deps);
	        foreach ($deps as $dep)
	        {
	            $depmod = ModuleLoader::instance()->loadModuleFS($dep, false, true);
	            
	            if (!$depmod)
	            {
	                continue;
	            }
	            
	            $deps = array_unique(array_merge($depmod->getDependencies(), $deps));
	        }
	    }

	    $back = array_unique($deps);
	    $back = (array_map(function(string $dep) { 
	        return ModuleLoader::instance()->getModule($dep, true, false);
	    }, $deps));
	    return $back;
	}
	
}
