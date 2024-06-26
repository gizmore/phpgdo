<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Core\Method\Welcome;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\DB\Query;
use GDO\Language\Trans;
use GDO\Net\GDT_Url;
use GDO\Table\GDT_Sort;
use GDO\UI\GDT_Container;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_HR;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;
use GDO\User\GDO_UserSettingBlob;
use GDO\User\GDT_ACL;
use GDO\User\GDT_ACLRelation;
use GDO\User\Module_User;
use GDO\Util\FileUtil;

/**
 * GDO base module class. Can manage config and user settings.
 *
 * 1337 lines is huge.
 *
 * @version 7.0.3
 * @since 2.0.1
 * @author gizmore
 */
class GDO_Module extends GDO
{

	# ###############
	# ## Override ###
	# ###############
	/**
	 * nocache appendix
	 */
	private static string $NC;

	public int $priority = 50;

	public string $version = '7.0.3';

	public string $license = GDO::LICENSE;

	/**
	 * @var string[] - The authors of a module, usually just,... me - gizmore :)
	 */
	protected array $authors = ['gizmore <gizmore@wechall.net>'];

	public function renderAuthors(): string
	{
		return implode("<br/>\n", $this->authors);
	}


	public bool $inited = false;

	/**
	 * @var GDT[]
	 */
	private array $configCache;

	/**
	 * @var array @TODO The config varcache should be in serialized filecache.
	 */
	private array $configVarCache = [];
	/**
	 *
	 * @var GDT[]
	 */
	private array $userConfigCache;

	/**
	 * @var GDT_ACL[]
	 */
	private array $settingsACL;

	/**
	 * @var GDT[]
	 */
	private array $settingsContainers;

	/**
	 * @var GDT[]
	 */
	private array $settingsConfigs;

	/**
	 * @var GDT[]
	 */
	private array $settingsSettings;

	/**
	 * Disable the process cache for modules.
	 */
	public function gdoCached(): bool
	{
		return false;
	}

	public function isTestable(): bool
	{
		return false;
	}

	public function defaultEnabled(): string
	{
		return $this->isSiteModule() ? GDT::ZERO : GDT::ONE;
	}

    public function defaultMethod(): Method
    {
        return Welcome::make();
    }

    public function isSiteModule(): bool
	{
		return false;
	}

	public function isCoreModule(): bool
	{
		return false;
	}

	# ###########
	# ## Info ###
	# ###########

	public function isInstallable(): bool
	{
		return true;
	}

	/**
	 * A list of required dependencies.
	 * Override this. Please add Core to your dependencies!
	 *
	 * @return string[]
	 */
	public function getDependencies(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	/**
	 * A list of optional modules that enhance this one.
	 * Override this.
	 *
	 * @return string[]
	 */
	public function getFriendencies(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	# #############
	# ## Events ###
	# #############

	/**
	 * Get GDT/ETC from config for current user that are privacy related.
	 *
	 * @return GDT[]
	 */
	public function getPrivacyRelatedFields(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	/**
	 * Run system checks for this module, for example if bcmath is installed.
	 * return errorSystemDependency('err_key') for a system dependency error.
	 */
	public function checkSystemDependencies(): bool
	{
		return true;
	}

	/**
	 * Skip these folders in unit tests using strpos.
	 * @return string[]
	 */
	public function thirdPartyFolders(): array
	{
		return [
			'bower_components/',
			'node_modules/',
			'vendor/',
			'3p/',
		];
	}

	/**
	 * Provided theme name in module /thm/$themeName/ folder.
	 */
	public function getTheme(): ?string
	{
		return null;
	}

	/**
	 * GDO classes to install.
	 *
	 * @return string[]
	 */
	public function getClasses(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	/**
	 * Get all license filenames.
	 */
	public function getLicenseFilenames(): array
	{
		return ['LICENSE'];
	}

	public function onWipe(): void {}

	public function onInstall(): void {}

	# ##########
	# ## GDO ###
	# ##########

	public function onAfterInstall(): void {}

	# Polymorph fix

	public function initOnce(): void
	{
		if (!$this->inited)
		{
			$this->inited = true;
//			if (!Application::$INSTANCE->isInstall())
			{
				$this->onModuleInit();
			}
		}
	}

	# Polymorph fix

	public function onModuleInit(): void {}

	# Polymorph fix

	public function onInitSidebar(): void {}

	# Polymorph fix fix

	public function onLoadLanguage(): void {}

	# #############
	# ## Static ###
	# #############

	public function onIncludeScripts(): void {}

//	public function gdoRealClassName(): string
//	{
//		return static::class;
//	}

	public function isEnabled(): bool
	{
		return $this->gdoValue('module_enabled');
	}

	# #############
	# ## Getter ###
	# #############

	public function enabled(bool $enabled): self
	{
		return $this->saveValue('module_enabled', $enabled);
	}

	public function isInstalled(): bool
	{
		return $this->isPersisted();
	}

	public function render_fs_version(): string
	{
		return $this->version;
	}

	/**
	 * HREF generation helper.
	 */
	public function href(string $methodName, string $append = ''): string
	{
		return href($this->getName(), $methodName, $append);
	}

	public function getName(): ?string
	{
		return $this->gdoVar('module_name') ?: $this->getModuleName();
	}

	public function gdoHumanName(): string
	{
		$name = $this->getModuleName();
		$key = strtolower("module_{$name}");
		return Trans::hasKey($key) ? Trans::t($key) : $name;
	}


	/**
	 * After creation reset the module version so the install form is up-to-date.
	 */
	public function gdoBeforeCreate(GDO $gdo, Query $query): void
	{
		$gdo->setVar('module_version', Module_Core::GDO_VERSION);
	}

	# ##############
	# ## Display ###
	# ##############

	public function __wakeup()
	{
		$this->inited = false;
		parent::__wakeup(); #PP#delete#
	}

	# ###########
	# ## Href ###
	# ###########

	public function hrefNoSEO(string $methodName, string $append = ''): string
	{
		return hrefNoSeo($this->getName(), $methodName, $append);
	}

	public function href_install_module(): string
	{
		return href('Admin', 'Install', '&module=' . $this->getName());
	}

	public function href_configure_module(): string
	{
		return href('Admin', 'Configure', '&module=' . $this->getName());
	}

	public function href_administrate_module(): ?string
	{
		return null;
	}

	public function canUpdate(): bool
	{
		return $this->version !== $this->getVersion()->__toString();
	}

	# ############
	# ## Hooks ###
	# ############

	public function getVersion(): ?Version
	{
		$value = $this->gdoValue('module_version');
		return $value instanceof Version ? $value : null;
	}

	# #############
	# ## Helper ###
	# #############

	public function canInstall(): bool
	{
		return !$this->isPersisted();
	}

	/**
	 * Filesystem path for a temp file.
	 * Absolute path to the gdo6/temp/{module}/ folder.
	 */
	public function tempPath(string $path = ''): string
	{
		$base = Application::$INSTANCE->isUnitTests() ? 'temp_test' : 'temp';
		$full = GDO_PATH . "{$base}/" . $this->getLowerName() . '/' . $path;
		FileUtil::createdDir(dirname($full));
		return $full;
	}

	# ###########
	# ## Path ###
	# ###########

	public function getLowerName(): string
	{
		return strtolower($this->getName());
	}

	public function storagePath(string $path = ''): string
	{
		$dir = GDO_PATH . GDO_FILES_DIR . '/' . $this->getLowerName() . '/';
		return FileUtil::createdDir($dir) . $path;
	}

	/**
	 * Render a template.
	 */
	public function php(string $path, array $tVars = null): string
	{
		return GDT_Template::php($this->getName(), $path, $tVars);
	}

	/**
	 * Get a file without include.
	 * Useful for assets with localized versions.
	 */
	public function templateFile(string $file): string
	{
		return GDT_Template::file($this->getName(), $file);
	}

	# ################
	# ## Templates ###
	# ################

	/**
	 * Get a GDT_Template.
	 * In JSON mode return a GDT_Array.
	 */
	public function responsePHP(string $file, array $tVars = null): GDT
	{
		switch (Application::$MODE_DETECTED)
		{
			case GDT::RENDER_JSON:
				return GDT_JSON::make()->value(...$tVars);
			default:
				return $this->templatePHP($file, $tVars);
		}
	}

	/**
	 * Get a GDT_Template.
	 */
	public function templatePHP(string $path, array $tVars = null): GDT
	{
		return GDT_Template::make()->template($this->getName(), $path, $tVars);
	}

	public function loadLanguage($path): self
	{
		Trans::addPath($this->filePath($path));
		return $this;
	}

	/**
	 * Filesystem path for a file within this module.
	 */
	public function filePath(string $path = ''): string
	{
		return rtrim(GDO_PATH, '/') . $this->wwwPath($path, '/');
	}

	# ###########
	# ## Init ###
	# ###########

	/**
	 * Relative www path for a resource.
	 */
	public function wwwPath(string $path = '', string $webRoot = GDO_WEB_ROOT): string
	{
		return $webRoot . "GDO/{$this->getName()}/{$path}";
	}

    public function wwwURL(string $path, string $webRoot = GDO_WEB_ROOT): string
    {
        return GDT_Url::absolute($this->wwwPath($path, $webRoot));
    }

	public function addConfigVarForCache(string $key, ?string $var): void
	{
		$this->configVarCache[$key] = $var;
	}

	public function &getConfigCache(): array
	{
		if (!isset($this->configCache))
		{
			$this->buildConfigCache();
		}
		return $this->configCache;
	}

	# ####################
	# ## Module Config ###
	# ####################

	/**
	 * Get module configuration hashed and cached.
	 *
	 * @return GDT[]
	 */
	public function &buildConfigCache(): array
	{
		if (!isset($this->configCache))
		{
			$this->configCache = [];
			foreach ($this->getConfig() as $gdt)
			{
				if ($name = $gdt->getName())
				{
					$this->configCache[$name] = $gdt;
					if (isset($this->configVarCache[$name]))
					{
						$var = $this->configVarCache[$name];
						$gdt->var($var);
					}
				}
			}
		}
		return $this->configCache;
	}

	/**
	 * Module config GDTs
	 *
	 * @return GDT[]
	 */
	public function getConfig(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	public function getConfigMemcache(): array
	{
		$key = $this->configCacheKey();
		if (null === ($cache = Cache::get($key)))
		{
			$cache = $this->buildConfigCache();
			Cache::set($key, $cache);
		}
		return $cache;
	}

	private function configCacheKey(): string
	{
		return $this->getName() . '_config_cache';
	}

	public function saveConfigValue(string $key, $value): void
	{
		GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->initialValue($value));
		Cache::remove('gdo_modules');
	}

	public function getConfigColumn(string $key): GDT
	{
		if (!isset($this->configCache))
		{
			$this->buildConfigCache();
		}
//		if (isset($this->configCache[$key]))
//		{
			return $this->configCache[$key];
//		}
//		return null;
	}

	/**
	 * Translated module name.
	 */
	public function renderName(): string
	{
		return $this->gdoHumanName();
	}

	public function &gdoColumnsCache(): array
	{
		return Database::columnsS(self::class);
	}

	public function gdoTableName(): string
	{
		return 'gdo_module';
	}

	public function gdoClassName(): string
	{
		return self::class;
	}

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('module_id'),
			GDT_Sort::make('module_priority'),
			GDT_Name::make('module_name')->notNull()->unique(),
			GDT_Version::make('module_version')->notNull(),
			GDT_Checkbox::make('module_enabled')->notNull()->initial('0'),
		];
	}

	public function removeConfigVar(string $key): void
	{
		$gdt = $this->getConfigColumn($key);
		GDO_ModuleVar::removeModuleVar($this, $gdt);
		Cache::remove('gdo_modules');
	}

	public function increaseConfigVar(string $key, float|int $by = 1): void
	{
		if ($by !== 0)
		{
			$this->saveConfigVar($key, (string)($this->getConfigValue($key) + $by));
		}
	}

	public function getConfigValue(string $key): float|object|int|bool|array|string|null
	{
		return $this->getConfigColumn($key)->getValue();
	}

	public function saveConfigVar(string $key, ?string $var): void
	{
		$gdt = $this->getConfigColumn($key);
		GDO_ModuleVar::createModuleVar($this, $gdt->initial($var));
		Cache::remove('gdo_modules');
	}

	# ##################
	# ## User config ###
	# ##################

	/**
	 * Get a current user setting.
	 */
	public function setting(string $key): GDT
	{
		return $this->userSetting(GDO_User::current(), $key);
	}

	/**
	 * Get a user setting.
	 */
	public function userSetting(GDO_User $user, string $key): GDT
	{
		$gdt = $this->getSetting($key);
		$settings = $this->loadUserSettings($user);
		if ($acl = $this->settingsACL[$key] ?? null)
		{
			$def = $this->getACLDefaultsFor($key);
			$acl->initialACL($def[0], $def[1], $def[2]);
		}
		if ($settings)
		{
			$gdt->setGDOData($settings);
		}
		return $gdt;
	}

	/**
	 * Get a setting var **without** user assign.
	 */
	private function getSetting(string $key): ?GDT
	{
		$this->buildSettingsCache();
		# Try by key
		if (isset($this->userConfigCache[$key]))
		{
			return $this->userConfigCache[$key]->reset();
		}
		# Try subcomponents foreach
		foreach ($this->userConfigCache as $gdt)
		{
			if (in_array($key, $gdt->gdoColumnNames(), true))
			{
				return $gdt->reset();
			}
		}
		return null;
	}

	# ###################
	# ## Settings API ###
	# ###################

	/**
	 * @return GDT[]
	 */
	public function &buildSettingsCache(): array
	{
		if (!isset($this->userConfigCache))
		{
			$this->userConfigCache = [];
			$this->settingsConfigs = GDT::EMPTY_ARRAY;
			$this->settingsSettings = GDT::EMPTY_ARRAY;
			$this->settingsACL = [];
			$this->settingsContainers = [];
			$configs = [];
			if ($config = $this->getUserSettings())
			{
				$configs = $config;
			}
			if ($config = $this->getUserSettingBlobs())
			{
				$configs = array_merge($configs, $config);
			}
			if ($configs)
			{
				$this->settingsSettings = $configs;
				$this->settingsContainers['div_set'] = GDT_Divider::make('div_sett')->label(
					'mt_account_settings');
				$this->_buildSettingsCacheB($configs, true);
			}
			if ($config = $this->getUserConfig())
			{
				$this->settingsConfigs = $config;
				$this->settingsContainers['div_conf'] = GDT_Divider::make('div_conf')->label('mt_account_config');
				$this->_buildSettingsCacheB($config, false);
			}
		}
		return $this->userConfigCache;
	}

	/**
	 * User changeable settings.
	 *
	 * @return GDT[]
	 */
	public function getUserSettings(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	/**
	 * User changeable settings in a blob table.
	 * For e.g. signature.
	 *
	 * @return GDT[]
	 */
	public function getUserSettingBlobs(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	private function _buildSettingsCacheB(array $gdts, bool $writeable): void
	{
		$first = true;
		foreach ($gdts as $gdt)
		{
			if (!$first)
			{
				$this->settingsContainers[] = GDT_HR::make();
			}
			else
			{
				$first = false;
			}
			$gdt->writeable($writeable);
			$this->_buildSettingsCacheC($gdt);
		}
	}

	private function _buildSettingsCacheC(GDT $gdt): void
	{
		$name = $gdt->getName();
		$this->userConfigCache[$name] = $gdt;
		$this->settingsContainers[$name] = $gdt;
		# If it's a saved field, build the ACL container.
		if ($gdt->isACLCapable())
		{
			if (!($gdt instanceof GDT_ACL))
			{
				$this->_buildSettingsCacheD($gdt);
			}
		}
	}

	private function _buildSettingsCacheD(GDT $gdt): void
	{
		$name = $gdt->getName();
		$acl = GDT_ACL::make("{$name}");
		$acl->setupLabels($gdt);
		$this->settingsACL[$name] = $acl;

		$relation = $acl->aclRelation;
		$level = $acl->aclLevel;
		$permission = $acl->aclPermission;

		$mu = Module_User::instance();
		$cont = GDT_Container::make()->horizontal();

		# Each var results in 3 GDT ACL vars in config cache.
		if ($mu->cfgACLRelations())
		{
			$cont->addField($relation);
		}

		if ($mu->cfgACLLevels())
		{
			$cont->addField($level);
		}

		if ($mu->cfgACLPermissions())
		{
			$cont->addField($permission);
		}

		# we add the GDT + a container with 3 acl fields to the container cache.
		$this->settingsContainers[] = $cont;
	}

	public static function instance(): static
	{
		return self::getByName(self::getNameS());
	}

	public static function getByName(string $moduleName): ?self
	{
		return ModuleLoader::$INSTANCE->getModule($moduleName);
	}

	public static function getNameS(): string
	{
		return substr(self::gdoShortNameS(), 7);
	}

	/**
	 * Config that the user cannot change.
	 *
	 * @return GDT[]
	 */
	public function getUserConfig(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	final public const SETTINGS_KEY = 'user_settings';

	private function loadUserSettings(GDO_User $user): array
	{
		if (null === ($settings = $user->tempGet(self::SETTINGS_KEY)))
		{
			$settings = self::loadUserSettingsB($user);
			$user->tempSet(self::SETTINGS_KEY, $settings);
		}
		return $settings;
	}

	/**
	 * Load a user's settings into their temp cache.
	 *
	 * @throws GDO_DBException
	 */
	private function loadUserSettingsB(GDO_User $user): array
	{
		if (!$user->isPersisted())
		{
			return GDT::EMPTY_ARRAY;
		}
		$uid = $user->getID();
		$settings = GDO_UserSetting::table()->select('uset_name, uset_var')->where("uset_user={$uid}");
		$blobs = GDO_UserSettingBlob::table()->select('uset_name, uset_var')->where("uset_user={$uid}");
		return $settings->union($blobs)
			->exec()
			->fetchAllArray2dPair();
	}

	public function getID(): ?string
	{
		return $this->gdoVar('module_id');
	}

	private function getACLDefaultsFor(string $key): array
	{
		$defaults = $this->getACLDefaults();
		if (isset($defaults[$key]))
		{
			$defaults[$key][1] = $defaults[$key][1] > 0 ? (string) $defaults[$key][1] : null;
			return $defaults[$key];
		}
		return [GDT_ACLRelation::NOONE, null, null];
	}

	protected function getACLDefaults(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	public function settingVar(string $key): ?string
	{
		return $this->userSettingVar(GDO_User::current(), $key);
	}

	# Cache

	public function userSettingVar(GDO_User $user, string $key): ?string
	{
		return $this->userSetting($user, $key)->getVar();
	}

	public function settingValue(string $key): mixed
	{
		return $this->userSettingValue(GDO_User::current(), $key);
	}

	public function userSettingValue(GDO_User $user, string $key): mixed
	{
		return $this->userSetting($user, $key)->getValue();
	}

	public function saveSetting(string $key, string $var): GDT
	{
		return self::saveUserSetting(GDO_User::current(), $key, $var);
	}

	/**
	 * @throws GDO_DBException
	 */
	public function saveUserSetting(GDO_User $user, string $key, ?string $var): GDT
	{
		$gdt = $this->userSetting($user, $key);
		$old = $gdt->var;
		if ($old === $var)
		{
			return $gdt;
		}
		if (!$user->getID())
		{
			return $gdt; # @TODO either persist the user on save setting or else?
		}
		if (!$gdt->validate($gdt->toValue($var)))
		{
			$this->error('err_invalid_user_setting', [
				$this->gdoHumanName(),
				$gdt->getName(),
				html($var),
				$gdt->renderError()]);
			return $gdt;
		}

		$data = $gdt->var($var)->getGDOData();
		$settings = $user->tempGet(self::SETTINGS_KEY);
		foreach ($data as $kk => $vv)
		{
			$settings[$kk] = $vv;
			$acl = $this->getACLDataFor($user, $gdt, $key);
			$data = [
				'uset_user' => $user->getID(),
				'uset_name' => $kk,
				'uset_var' => $vv,
				'uset_relation' => $acl[0],
				'uset_level' => $acl[1],
				'uset_permission' => $acl[2],
			];
			$entry = ($gdt instanceof GDT_Text) ?
				GDO_UserSettingBlob::blank($data) :
				GDO_UserSetting::blank($data);
			$entry->softReplace();
		}
		GDT_Hook::callHook('UserSettingChanged', $user, $key, $old, $var);
		$user->tempSet(self::SETTINGS_KEY, $settings);
		return $gdt;
	}

	public function error(string $key, array $args = null): bool
	{
		Website::error($this->gdoHumanName(), $key, $args);
		return false;
	}

	private function getACLDataFor(GDO_User $user, GDT $gdt, string $key): array
	{
		$cache = $this->_getACLDataCacheFor($user);
		return isset($cache[$key]) ?
			$cache[$key]->toACLData() :
			$this->getACLDefaultsFor($key);
	}

	/**
	 * @return GDO_UserSetting[]
	 */
	private function _getACLDataCacheFor(GDO_User $user): array
	{
		try
		{
			$key = "uset_acl_{$user->getID()}";
			if (null === ($cache = Cache::get($key)))
			{
				$uid = $user->getID();
				$query1 = GDO_UserSetting::table()->select('uset_name, gdo_usersetting.*')->where('uset_user=' . $uid);
				$query2 = GDO_UserSettingBlob::table()->select('uset_name, gdo_usersettingblob.*')->where('uset_user=' . $uid);
				$cache = $query1->union($query2)->exec()->fetchAllArray2dObject();
				Cache::set($key, $cache);
			}
			return $cache;
		}
		catch (\Throwable $ex)
		{
			Debug::debugException($ex);
			return [];
		}
	}

	public function saveUserSettingACLRelation(GDO_User $user, string $key, string $relation): void
	{
		$this->saveUserSettingACL($user, $key, 'uset_relation', $relation);
	}

	private function saveUserSettingACL(GDO_User $user, string $key, string $aclField, ?string $aclVar): void
	{
		$gdt = $this->getSetting($key);
		if ($gdt instanceof GDT_Text)
		{
			GDO_UserSettingBlob::updateACL($user, $gdt, $aclField, $aclVar);
		}
		else
		{
			GDO_UserSetting::updateACL($user, $gdt, $aclField, $aclVar);
		}
	}


	public function saveUserSettingACLLevel(GDO_User $user, string $key, ?string $level): void
	{
		$this->saveUserSettingACL($user, $key, 'uset_level', $level);
	}

	public function saveUserSettingACLPermission(GDO_User $user, string $key, ?string $permission): void
	{
		$this->saveUserSettingACL($user, $key, 'uset_permission', $permission);
	}

	public function increaseSetting(string $key, int|float $by = 1): GDT
	{
		return $this->increaseUserSetting(GDO_User::current(), $key, $by);
	}

	public function increaseUserSetting(GDO_User $user, string $key, int|float $by = 1): GDT
	{
		$new = (string) ($this->userSettingValue($user, $key) + $by);
		return $this->saveUserSetting($user, $key, $new);
	}

	public function hasUserSettings(): bool
	{
		foreach ($this->getSettingsCache() as $gdt)
		{
			if (!$gdt->isHidden())
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @return GDT[]
	 */
	public function &getSettingsCache(): array
	{
		if (!isset($this->userConfigCache))
		{
			$this->buildSettingsCache();
		}
		return $this->userConfigCache;
	}

	public function &getSettingsConfigs(): array
	{
		if (!isset($this->userConfigCache))
		{
			$this->buildSettingsCache();
		}
		return $this->settingsConfigs;
	}

	public function &getSettingsSettings(): array
	{
		if (!isset($this->userConfigCache))
		{
			$this->buildSettingsCache();
		}
		return $this->settingsSettings;
	}

	public function &getSettingsCacheContainers(): array
	{
		if (!isset($this->userConfigCache))
		{
			$this->buildSettingsCache();
		}
		return $this->settingsContainers;
	}

	######################
	### Settings Query ###
	######################

	public function hasSetting(string $key): bool
	{
		$this->buildSettingsCache();
		return isset($this->userConfigCache[$key]);
	}

	# ##########
	# ## ACL ###
	# ##########

	/**
	 * Get the ACL field for a user-setting gdt.
	 */
	public function getSettingACL(string $name): ?GDT_ACL
	{
		$this->buildSettingsCache();
		return $this->settingsACL[$name] ?? null;
	}

	/**
	 * Join the settings table to a user field.
	 */
	public function joinSetting(Query $query, string $key, string $userFieldName = 'gdo_user.user_id'): Query
	{
		$setting = $this->getSetting($key);
		$default = quote($setting->getInitial());
		$jn = "usetjoin_{$key}";
		return $query->select("IFNULL( ( SELECT uset_var FROM gdo_usersetting {$jn} WHERE {$jn}.uset_name='{$key}' AND {$jn}.uset_user={$userFieldName} ), {$default} ) AS {$key}");
	}

	/**
	 * @throws GDO_DBException
	 */
	public function getUserConfigACLField(string $key, GDO_User $user = null): ?GDT_ACL
	{
		$c = $this->settingsACL;
		$user = $user ?: GDO_User::current();
		return isset($c[$key]) ? $this->_ucacl($key, $c[$key], $user) : null;
	}

	/**
	 * @throws GDO_DBException
	 */
	private function _ucacl(string $key, GDT_ACL $acl, GDO_User $user): GDT_ACL
	{
		$gdt = $this->userSetting($user, $key);
		foreach (array_keys($gdt->getGDOData()) as $k)
		{
			$data = $this->getACLDataFor($user, $gdt, $k);
			$acl->aclRelation->var($data[0]);
			$acl->aclLevel->var($data[1]);
			$acl->aclPermission->var($data[2]);
		}
		return $acl;
	}

	public function getMethod(string $methodName): ?Method
	{
		$methods = $this->getMethods(false);
		foreach ($methods as $method)
		{
			if (strcasecmp($methodName, $method->gdoShortName()) === 0)
			{
				return $method;
			}
		}
		return null;
	}

	/**
	 * Get all methods for this module.
	 *
	 * @return Method[]
	 */
	public function getMethods(bool $withPermission = true): array
	{
		$path = $this->filePath('Method');
		if (!FileUtil::isDir($path))
		{
			return GDT::EMPTY_ARRAY;
		}
		$methods = scandir($path);
		$methods = array_map(function ($file)
		{
			return substr($file, 0, -4);
		}, $methods);
		$methods = array_filter($methods, function ($file)
		{
			return !!$file;
		});
		$methods = array_map(
			function ($file)
			{
				$className = "\\GDO\\{$this->getName()}\\Method\\{$file}";
				return call_user_func([
					$className,
					'make',
				]);
			}, $methods);
		if ($withPermission)
		{
			$methods = array_filter($methods,
				function (Method $method)
				{
					$error = '';
					$args = [];
					return $method->hasPermission(GDO_User::current(), $error, $args);
				});
		}
		return $methods;
	}

	/**
	 * Get a method by name.
	 * Case insensitive.
	 *
	 * @throws GDO_Exception
	 */
	public function getMethodByName(string $methodName, bool $throw = true): ?Method
	{
		$files = scandir($this->filePath('Method'));
		foreach ($files as $file)
		{
			$file = substr($file, 0, -4);
			if (strcasecmp($methodName, $file) === 0)
			{
				$className = "\\GDO\\{$this->getName()}\\Method\\{$file}";
				return call_user_func([
					$className,
					'make',
				]);
			}
		}
		if ($throw)
		{
			throw new GDO_Exception('err_unknown_method', [
				$this->renderName(),
				html($methodName),
			]);
		}
		return null;
	}

	# #############
	# ## Method ###
	# #############
	public function getMethodNames(bool $withPermission = true): array
	{
		$methods = $this->getMethods($withPermission);
		return array_map(function (Method $method)
		{
			return $method->gdoShortName();
		}, $methods);
	}

	/**
	 * Get the ".min" file suffix in case we want minification.
	 */
	public function cfgMinAppend(): string
	{
		$mode = self::config_var('Javascript', 'minify_js', 'no');
		return $mode === 'no' ? GDT::EMPTY_STRING : '.min';
	}

	/**
	 * Helper to get the config var for a module.
	 */
	public static function config_var(string $moduleName, string $key, string $default = null): ?string
	{
		if ($module = ModuleLoader::instance()->getModule($moduleName, false))
		{
			return $module->getConfigVar($key);
		}
		return $default;
	}

	public function getConfigVar(string $key): ?string
	{
		if ($gdt = $this->getConfigColumn($key))
		{
			return $gdt->getVar();
		}
		return null;
	}

	# #############
	# ## Assets ###
	# #############

	public function addBowerJS(string $path): void
	{
		$this->addJS('bower_components/' . $path);
	}

	public function addJS(string $path): void
	{
		$nc = $this->nocacheVersion();
		Javascript::addJS($this->wwwPath("{$path}?{$nc}"));
	}

	/**
	 * Get the cache poisoner.
	 * Base is gdo revision string.
	 * Additionally a cache clear triggers an increase of the assets version.
	 */
	public function nocacheVersion(): string
	{
		if (!isset(self::$NC))
		{
			$v = Module_Core::GDO_REVISION;
			$av = Module_Core::instance()->cfgAssetVersion();
			self::$NC = "_v={$v}&_av={$av}";
		}
		return self::$NC;
	}

	public function addBowerCSS(string $path): void
	{
		$this->addCSS('bower_components/' . $path);
	}

	public function addCSS(string $path): void
	{
		$nc = $this->nocacheVersion();
		$path = $this->wwwPath("{$path}?{$nc}");
		CSS::addFile($path);
	}

	public function message(string $key, array $args = null): bool
	{
		Website::message($this->gdoHumanName(), $key, $args);
		return true;
	}

	public function getCLITrigger(): string
	{
		return strtolower($this->getModuleName());
	}

	protected function errorSystemDependency(string $key, array $args = null): bool
	{
		return $this->error('err_system_dependency', [
			$this->gdoHumanName(),
			t($key, $args),
		]);
	}

	protected function warningSystemDependency(string $key, array $args = null): bool
	{
		return $this->message('warn_system_dependency', [
			$this->gdoHumanName(),
			t($key, $args),
		]);
	}

	public function isGDOLicense(): bool
	{
		return $this->license == GDO::LICENSE;
	}


}
