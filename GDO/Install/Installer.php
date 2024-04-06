<?php
declare(strict_types=1);
namespace GDO\Install;

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDO_Module;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Core\ModuleProviders;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Install\Method\Configure;
use GDO\User\GDO_Permission;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;
use GDO\Util\Strings;

/**
 * Install helper.
 *
 * @version 7.0.3
 * @since 4.0.0
 * @author gizmore
 */
class Installer
{

	/**
	 * @throws GDO_DBException
	 */
	public static function installModuleWithDependencies(GDO_Module $module, bool $forceMigrate = false): void
	{
		$modules = self::getDependencyModules($module->getName());
		$modules[] = $module;
		self::installModules($modules, $forceMigrate);
	}

	/**
	 * @return GDO_Module[]
	 */
	public static function getDependencyModules(string $moduleName, bool $core = false): array
	{
		return self::toModules(self::getDependencyNames($moduleName, $core));
	}

	/**
	 * @return string[]
	 */
	public static function getDependencyNames(string $moduleName, bool $core = false): array
	{
		return self::getDepModuleNames($moduleName, false, $core);
	}

	/**
	 * @return GDO_Module[]
	 */
	public static function getFriendencyModules(string $moduleName, bool $core = false): array
	{
		return self::toModules(self::getFriendencyModules($moduleName, $core));
	}


	/**
	 * @return string[]
	 */
	public static function getFriendencyNames(string $moduleName, bool $core = false): array
	{
		return self::getDepModuleNames($moduleName, true, $core);
	}


	/**
	 * Return all modules needed for a module.
	 * Used in phpgdo-docs to generate a module list for a single module documentation output.
	 *
	 * @return string[]
	 */
	private static function getDepModuleNames(string $moduleName, bool $friends = false, bool $core = false): array
	{
		$module = ModuleLoader::instance()->loadModuleFS($moduleName, true, false);
		$moduleName = $module->getModuleName();
		$deps = $module->getDependencies();
		$frds = $module->getFriendencies();
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

		$deps = $friends ? $frds : $deps;

		$deps = array_filter($deps,
			function (string $name) use ($moduleName, $core)
			{
				if (!$core)
				{
					if (ModuleProviders::isCoreModule($name))
					{
						return false;
					}
				}
				return $name !== $moduleName;
			});
		sort($deps);
		return $deps;
	}

	/**
	 * Turn modules into their names... gnarf
	 *
	 * @param GDO_Module[] $modules
	 *
	 * @return string[]
	 */
	public static function toNames(array $modules): array
	{
		return array_map(function (GDO_Module $m)
		{
			return $m->getName();
		}, $modules);
	}

	/**
	 * Turn names into their modules.
	 *
	 * @param string[] $names
	 *
	 * @return GDO_Module[]
	 */
	public static function toModules(array $names): array
	{
		$loader = ModuleLoader::instance();
		return array_map(function (string $moduleName) use ($loader)
		{
			return $loader->getModule($moduleName);
		}, $names);
	}

	/**
	 * @param GDO_Module[] $modules
	 *
	 * @throws GDO_DBException
	 */
	public static function installModules(array $modules, bool $forceMigrate = false): bool
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

		usort($modules, function (GDO_Module $a, GDO_Module $b)
		{
			return $a->priority - $b->priority;
		});

		/**
		 * @var GDO_Module $module
		 */
		foreach ($modules as $module)
		{
			if (!$module->isInstallable())
			{
				continue;
			}
			if (($isInstall && $isCLI) || ($isTest))
			{
				echo "Installing {$module->getName()}\n";
				flush();
			}
			self::installModule($module, $forceMigrate);
		}
		return true;
	}

	/**
	 * @throws GDO_DBException
	 */
	public static function installModule(GDO_Module $module, bool $forceMigrate = false): void
	{
		if (!$module->isInstallable())
		{
			return;
		}
		self::installModuleClasses($module, $forceMigrate);

		if (!$module->isPersisted())
		{
            if (!($module2 = GDO_Module::getBy('module_name', $module->getModuleName())))
            {
                $module2 = GDO_Module::blank([
                    'module_name' => $module->getModuleName(),
                    'module_enabled' => '1',
                    'module_version' => $module->version,
                    'module_priority' => (string) $module->priority,
                ])->insert();

                $module->setVars([
                    'module_id' => $module2->getID(),
                    'module_name' => $module->getModuleName(),
                    'module_enabled' => '1',
                    'module_version' => $module->version,
                    'module_priority' => (string) $module->priority,
                ]);
            }
            else
            {
                $module->setVars($module2->getGDOVars());
                ModuleLoader::instance()->setModule($module);
            }
		}
		else
		{
			ModuleLoader::instance()->setModule($module);
		}

		$upgraded = false;
		while ($module->getVersion()->__toString() < $module->version)
		{
			self::upgrade($module);
			$upgraded = true;
		}

		if ($forceMigrate || $upgraded)
		{
			self::recreateDatabaseSchema($module);
		}

		self::installMethods($module);

        $module->initOnce();
		$module->onInstall();

		ModuleLoader::instance()->addEnabledModule($module);
	}

	public static function installModuleClasses(GDO_Module $module): void
	{
		if ($classes = $module->getClasses())
		{
			foreach ($classes as $class)
			{
				if (is_subclass_of($class, 'GDO\Core\GDO'))
				{
					if ($gdo = $class::table())
					{
						self::installModuleClass($gdo);
					}
				}
			}
		}
	}

	public static function installModuleClass(GDO $gdo): void
	{
		$gdo->createTable();
	}

	/**
	 * @throws GDO_DBException
	 */
	public static function upgrade(GDO_Module $module): void
	{
		$version = self::increaseVersion($module, false);
		self::upgradeTo($module, $version);
		self::increaseVersion($module, true);
	}

	/**
	 * Increase version by one patch level.
	 *
	 * @throws GDO_DBException
	 */
	private static function increaseVersion(GDO_Module $module, bool $write): string
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

	/**
	 * On an upgrade we execute a possible upgrade file.
	 * We also recreate the database schema.
	 */
	public static function upgradeTo(GDO_Module $module, string $version): void
	{
		self::includeUpgradeFile($module, $version);
		self::recreateDatabaseSchema($module, $version);
	}


	public static function includeUpgradeFile(GDO_Module $module, string $version): void
	{
		$upgradeFile = $module->filePath("upgrade/$version.php");
		if (FileUtil::isFile($upgradeFile))
		{
			include($upgradeFile);
		}
	}

	/**
	 * Recreate a database schema.
	 * I call this "automigration".
	 * COPY table. DROP table. CREATE table. RE-IMPORT table. Works :)
	 *
	 * @version 7.0.3
	 * @since 6.11.5
	 */
	public static function recreateDatabaseSchema(GDO_Module $module): void
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
					$gdo = call_user_func([$classname, 'table']);
                    if (!$gdo)
                    {
                        xdebug_break();
                    }
					if ($gdo->gdoIsTable())
					{
						Database::DBMS()->dbmsAutoMigrate($gdo);
					}
				}
			}
			catch (GDO_DBException $t)
			{
				echo Debug::debugException($t);
			}
			finally
			{
				$db->enableForeignKeyCheck();
			}
		}
	}

	public static function installMethods(GDO_Module $module): void
	{
		self::loopMethods($module, [__CLASS__, 'installMethod']);
	}

	public static function loopMethods(GDO_Module $module, $callback): void
	{
		$dir = $module->filePath('Method');
		if (FileUtil::isDir($dir))
		{
			Filewalker::traverse($dir, null, $callback, null, 0, $module);
		}
	}

	public static function dropModule(GDO_Module $module): void
	{
		$db = Database::instance();
		try
		{
			$db->disableForeignKeyCheck();
			$module->onWipe();
			self::dropModuleClasses($module);
			$module->delete();
            GDO_Module::table()->deleteWhere("module_name='{$module->getName()}'");
			Cache::remove('gdo_modules');
		}
		catch (GDO_DBException $ex)
		{
			echo Debug::debugException($ex);
		}
		finally
		{
			$db->enableForeignKeyCheck();
		}
	}

	public static function dropModuleClasses(GDO_Module $module): void
	{
		if ($classes = $module->getClasses())
		{
			foreach (array_reverse($classes) as $class)
			{
				if (is_subclass_of($class, 'GDO\\Core\\GDO'))
				{
					$gdo = $class::table();
					/** @var GDO $gdo * */
					if (!$gdo->gdoAbstract())
					{
						$gdo->dropTable();
					}
				}
			}
		}
	}

	public static function installMethod($entry, $path, GDO_Module $module): void
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

	/**
	 * Helper to get the method for a method loop.
	 *
	 * @deprecated because the naming is horrible. Also the logic is not nice. @TODO: Put methods in the database!
	 */
	public static function loopMethod(GDO_Module $module, string $path): Method
	{
		$entry = Strings::substrTo(basename($path), '.');
		$class_name = "GDO\\{$module->getName()}\\Method\\$entry";
		if (!class_exists($class_name, false))
		{
			include $path;
		}
		return $module->getMethod($entry);
	}


	########################
	### Config Refresher ###
	########################

	/**
	 * In case a new config.php variable is introduced, this method can upgrade your config.
	 */
	public static function refreshConfig(string $path): bool
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
	public static function getModuleDescription(GDO_Module $module, bool $short=false): string
	{
		$back = '';
		if ($readme = FileUtil::getContents($module->filePath('README.md')))
		{
			$matches = null;
			if (preg_match("/^#.*[\\r\\n]+([^#]+)/", $readme, $matches))
			{
				$back .= trim($matches[1]);
			}
		}

		if ($short)
		{
			return $back;
		}

		$back .=  "<br/>\n<br/>\n";

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
	public static function getClassDescription(object $object): ?string
	{
		$klass = get_class($object);
		return self::getClassNameDescription($klass);
	}

	/**
	 * Get a class' phpdoc description.
	 */
	public static function getClassNameDescription(string $klass): ?string
	{
		$klass = str_replace('\\', '/', $klass);
		$filename = GDO_PATH . $klass . '.php';
		if ($sourcecode = FileUtil::getContents($filename))
		{
			$matches = null;
			if (preg_match_all("/[\r\n]\/\*\*[\s*\r\n]*([.\s\w]+)/", $sourcecode, $matches))
			{
				return trim($matches[1][0]) . "<br/>\n";
			}
		}
		return null;
	}

}
