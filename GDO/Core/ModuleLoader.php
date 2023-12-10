<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Language\Trans;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;
use Throwable;

/**
 * Module loader.
 * Can load from DB and/or FS.
 * Uses memcached for fast modulecache loading.
 *
 * @version 7.0.3
 * @since 3.0.0
 * @author gizmore
 *
 * @see GDO_Module
 */
final class ModuleLoader
{

	public static self $INSTANCE;

	/**
	 * @var GDO_Module[]
	 */
	private static array $INSTANCES = [];

	/**
	 * Base modules path, the modules folder.
	 */
	private string $path;

	/**
	 * @var GDO_Module[]
	 */
	private array $modules;

	#############
	### Cache ###
	#############
	/**
	 * @var GDO_Module[]
	 */
	private array $enabledModules;
	private bool $scriptsIncluded = false;
	private bool $loadedDB = false;
	private bool $loadedFS = false;

	public function __construct(string $path)
	{
		self::$INSTANCE = $this;
		$this->path = $path;
	}

	public static function instance(): self
	{
		return self::$INSTANCE;
	}

	/**
	 * Get all loaded modules.
	 *
	 * @return GDO_Module[]
	 */
	public function &getModules(): array
	{
		return $this->modules;
	}

	public function addEnabledModule(GDO_Module $module): bool
	{
		if (isset($this->enabledModules))
		{
			if (!in_array($module, $this->getEnabledModules(), true))
			{
//				$this->initModuleVars();
				$module->initOnce();
				$this->enabledModules[] = $module;
			}
			return true;
		}
		return false;
	}

	/**
	 * Get all enabled and loaded modules.
	 *
	 * @return GDO_Module[]
	 */
	public function &getEnabledModules(): array
	{
		if (!isset($this->enabledModules))
        {
//            $this->sortModules();
            # Filter
            $this->enabledModules = array_filter($this->modules, function (GDO_Module $module)
			{
				return $module->isEnabled();
			});
		}
		return $this->enabledModules;
	}

	#################
	### Cacheload ###
	#################

	/**
	 * Load module vars from database.
	 */
	public function initModuleVars(): void
	{
		# Query all module vars
		try
		{
			if (GDO_DB_ENABLED)
			{
				if (null === ($data = Cache::fileGetSerialized('gdo_modulevars')))
				{
					$query = GDO_ModuleVar::table()->select('mv_module, mv_name, mv_value');
					$data = $query->exec()->fetchAllRows();
					Cache::fileSetSerialized('gdo_modulevars', $data);
				}
				# Assign them to the modules
				foreach ($data as $row)
				{
					$module = $this->getModuleByID($row[0]);
                    if (!$module)
                    {
                        xdebug_break();
                    }
					$module->addConfigVarForCache($row[1], $row[2]);
				}
			}
		}
		catch (Throwable $ex)
		{
			echo Debug::debugException($ex);
		}
	}

	/**
	 * Get a module by ID.
	 */
	public function getModuleByID(string $moduleID): ?GDO_Module
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

	public function setModule(GDO_Module $module): void
	{
		$name = strtolower($module->getName());
		$this->modules[$name] = $module;
	}

	public function getModule(string $moduleName, bool $fs = true): ?GDO_Module
	{
		$caseName = $moduleName;
		$moduleName = strtolower($moduleName);
		if (isset($this->modules[$moduleName]))
		{
			return $this->modules[$moduleName];
		}
		return $fs ? $this->loadModuleFS($caseName) : null;
	}

	/**
	 * Load a module from filesystem if it is not loaded yet.
	 */
	public function loadModuleFS(string $name, bool $dirty = true): ?GDO_Module
	{
		$lowerName = strtolower($name);

		if (!isset($this->modules[$lowerName]))
		{
			$className = "GDO\\$name\\Module_$name";
			if (class_exists($className))
			{
				$moduleData = GDO_Module::getBlankData([
					'module_name' => $name,
					'module_enabled' => $dirty ? '0' : '1',
				]);
				if ($module = self::instanciate($moduleData, $dirty))
				{
					$this->modules[$lowerName] = $module;
				}
			}
			else
			{
				return null;
			}
		}
		if (!$dirty)
		{
			$this->addEnabledModule($this->modules[$lowerName]);
		}
		return $this->modules[$lowerName];
	}

	/**
	 * Instanciate a module from gdoVars/loaded data.
	 */
	private static function instanciate(array $moduleData, bool $dirty = false): GDO_Module
	{
		$name = $moduleData['module_name'];
		$klass = "GDO\\$name\\Module_$name";
		/** @var GDO_Module $instance */
		$instance = self::$INSTANCES[$name] ?? (self::$INSTANCES[$name] = call_user_func([$klass, 'tableGDO']));
		$moduleData['module_priority'] = (string) $instance->priority;
		$moduleData['module_enabled'] = $dirty ?
				'0' : $moduleData['module_enabled'];
		$instance->setGDOVars($moduleData, $dirty);
		return $instance;
	}

	##################
	### Massloader ###
	##################

	/**
	 * Load active modules, preferably from cache.
	 * Sorted by priority to be spinlock free.
	 *
	 * @throws GDO_Exception
	 * @return GDO_Module[]
	 */
	public function loadModulesCache(): array
	{
		static $key = 'gdo_modules';
		if (null === ($cache = Cache::get($key)))
		{
			$cache = $this->loadModulesA();
			Cache::set($key, $cache);
		}
		else
		{
			$this->initFromCache($cache);
		}
		return $this->modules;
	}

	/**
	 * @return GDO_Module[]
	 */
	public function loadModulesA(): array
	{
		$modules = $this->loadModules(!!GDO_DB_ENABLED, !GDO_DB_ENABLED);
		$this->initModules();
		return $modules;
	}

	/**
	 * Load all modules.
	 * @return GDO_Module[]
	 */
	public function loadModules(bool $loadDB = true, bool $loadFS = false): array
	{
		# Load maybe 0, 1 or 2 sources
		$loaded = false;
		if ($loadDB && (!$this->loadedDB))
		{
			$loaded = $this->loadedDB = $this->loadModulesDB() !== false;
		}

		if ($loadFS && (!$this->loadedFS))
		{
			$this->loadModulesFS(false);
			$loaded = $this->loadedFS = true;
		}

		# Loaded one?
		if ($loaded)
		{
			$this->modules = $this->sortModules();
		}
		return $this->modules;
	}

	/**
	 * Force module reloading.
	 */
	public function reset(): self
	{
		$this->loadedDB = false;
		$this->loadedFS = false;
		$this->modules = [];
		unset($this->enabledModules);
		return $this;
	}

	private function loadModulesDB(): ?array
	{
		if (!GDO_DB_ENABLED)
		{
			return null;
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
							$this->modules[$moduleName] = $module->setPersisted();
						}
					}
					catch (Throwable)
					{
						echo "A module file or folder is missing in filesystem: GDO/{$moduleName}(\n";
					}
				}
				else
				{
					$module = $this->modules[$moduleName];
					$module->setVars($moduleData, false);
					$module->setPersisted();
				}
			}
			return $this->modules;
		}
		catch (GDO_DBException $ex)
		{
			if (Application::instance()->isInstall())
			{
				fwrite(STDERR, "A Database exception occured.\nOn installations this might be fine, as the gdo_module table does not exist yet.\n");
			}
			else
			{
				Debug::debugException($ex);
			}
			return null;
		}
	}

	private function loadModulesFS(bool $init = true): void
	{
// 	    Trans::inited(false);
		Filewalker::traverse($this->path, null, null, [$this, '_loadModuleFS'], 0, $init);
//		Trans::inited(true);
		$this->sortModules();
		if ($init)
		{
			foreach ($this->modules as $module)
			{
				$module->buildConfigCache();
				$module->buildSettingsCache();
			}
		}
	}

	public function sortModules(): array
	{
		uasort($this->modules, function (GDO_Module $a, GDO_Module $b)
		{
			return $a->priority - $b->priority;
		});
		return $this->modules;
	}

	/**
	 * @throws GDO_Exception
	 */
	private function setupCLIAliases(): void
	{
		if (null === ($cache = Cache::get('cli_aliases')))
		{
			$cache = $this->generateCLIAliases();
			Cache::set('cli_aliases', $cache);
		}
		Method::$CLI_ALIASES = $cache;
	}

	/**
	 * @throws GDO_Exception
	 */
	private function generateCLIAliases(): array
	{
		$cache = [];
		foreach ($this->getEnabledModules() as $module)
		{
			foreach ($module->getMethods(false) as $method)
			{
				if ($method->isCLI())
				{
					$alias = $method->getCLITrigger();
					if (isset($cache[$alias]))
					{
						throw new GDO_Exception('Duplicate method CLI Trigger: ' . $alias);
					}
					$cache[$alias] = get_class($method);
				}
			}
		}
		return $cache;
	}

	/**
	 * @throws GDO_Exception
	 */
	private function initFromCache(array $cache): void
	{
		$this->modules = $cache;
		if (Application::$INSTANCE->isCLI())
		{
			$this->setupCLIAliases();
		}
	}

	public function loadLangFiles(): void
	{
		foreach ($this->modules as $module)
		{
			$module->onLoadLanguage();
		}
	}

	public function initModules(): bool
	{
		try
		{
			foreach ($this->getEnabledModules() as $module)
			{
				if ($theme = $module->getTheme())
				{
					GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
				}
			}
            $this->initModuleVars();
            return $this->initModulesB();
		}
		catch (\Throwable $ex)
		{
			echo Debug::debugException($ex);
			return false;
		}
	}

	/**
	 * @throws GDO_Exception
	 */
	private function initModulesB(): bool
	{
		# Init modules
		$app = Application::$INSTANCE;
		Trans::inited();
		if (!$app->isInstall())
		{
			foreach ($this->getEnabledModules() as $module)
			{
				$module->initOnce();
			}
		}
		if ($app->isUnitTests())
		{
			$this->setupCLIAliases();
			$this->onIncludeScripts();
		}
		elseif ($app->isCLI())
		{
			$this->setupCLIAliases();
		}
		else
		{
			$this->onIncludeScripts();
		}
		return true;
	}

	############
	### Vars ###
	############

	public function onIncludeScripts(): void
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

	public function _loadModuleFS(string $entry, string $path): void
	{
		if (FileUtil::isFile("$path/Module_$entry.php"))
		{
			if (!str_starts_with($entry, 'phpgdo-'))
			{
				$this->loadModuleFS($entry);
			}
		}
	}

}
