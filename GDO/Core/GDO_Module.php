<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\User\GDO_UserSetting;
use GDO\User\GDO_UserSettingBlob;
use GDO\User\GDO_User;
use GDO\Tests\Module_Tests;
use GDO\Util\FileUtil;
use GDO\Util\Strings;
use GDO\Table\GDT_Sort;
use GDO\UI\GDT_Divider;
use GDO\User\GDT_ACL;
use GDO\UI\GDT_Container;
use GDO\UI\GDT_HR;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Error;

/**
 * GDO base module class.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 2.0.1
 */
class GDO_Module extends GDO
{
	################
	### Override ###
	################
	public int $priority = 50;
	
	public string $version = "7.0.1";
	public string $license = "GDOv7-LICENSE";
	public string $authors = "gizmore <gizmore@wechall.net>";
	
	public function gdoCached() : bool { return false; }
	public function memCached() : bool { return false; }
	
	public function defaultEnabled() : bool { return !$this->isSiteModule(); }
	public function isCoreModule() : bool { return false; }
	public function isSiteModule() : bool { return false; }
	public function isInstallable() : bool { return true; }
	
	### Hax
	public function isTestable() : bool { return false; } # Overrides the GDT behaviour. GDO_Module is abstract.
	
	/**
	 * A list of required dependencies.
	 * Override this. Please add Core to your dependencies!
	 * @return string[]
	 */
	public function getDependencies() : array
	{
		return GDT::EMPTY_GDT_ARRAY;
	}
	
    /**
     * A list of optional modules that enhance this one.
     * Override this.
	 * @return string[]
	 */
	public function getFriendencies() : array
	{
		return GDT::EMPTY_GDT_ARRAY;
	}
	
	/**
	 * Run system checks for this module, for example if bcmath is installed.
	 * return errorSystemDependency('err_key') for a system dependency error.
	 * @see Module_Core
	 */
	public function checkSystemDependencies() : bool
	{
		return true;
	}
	
    /**
	 * Skip these folders in unit tests using strpos.
	 * @see Module_Tests
	 * @return string[]
	 */
	public function thirdPartyFolders() : array
	{
		return [
			'/bower_components/',
			'/node_modules/',
			'/vendor/',
			'/3p/',
		];
	}
	
	/**
	 * Provided theme name in module /thm/$themeName/ folder.
	 * @return string $themeName
	 */
	public function getTheme() : ?string { return null; }
	
	/**
	 * GDO classes to install.
	 * @return string[]
	 */
	public function getClasses() : array { return GDT::EMPTY_GDT_ARRAY; }
	
	/**
	 * Module config GDTs
	 * @return GDT[]
	 */
	public function getConfig() : array { return GDT::EMPTY_GDT_ARRAY; }
	
	############
	### Info ###
	############
	/**
	 * Translated module name.
	 */
	public function renderName() : string
	{
		$name = $this->getName();
		$key = strtolower("module_{$name}");
		return Trans::hasKey($key) ? t($key) : $name;
	}
	
	public function getLowerName() : string
	{
	    return strtolower($this->getName());
	}
	
	public function displayModuleLicense() : string
	{
		return $this->getModuleLicense();
	}
	
	public function getLicenseFilenames() : array
	{
	    return [
	        'LICENSE',
	    ];
	}
	
	/**
	 * Module description is fetched from README.md by default.
	 * @return string
	 */
	public function getModuleDescription() : ?string
	{
		if ($readme = @file_get_contents($this->filePath('README.md')))
		{
			$matches = null;
			if (preg_match("/^#.*[\\r\\n]+([^#]+)#?/", $readme, $matches))
			{
				return trim($matches[1]);
			}
		}
		return null;
	}
	
	################
	### Abstract ###
	################
	public function onWipe() : void {}
	public function onInstall() : void {}
	public function onAfterInstall() : void {}
	public function onInit() {}
	public function onInitCLI() : void {}
	public function onInitSidebar() : void {}
	public function onLoad() : void {}
	public function onLoadLanguage() : void {}
	public function onIncludeScripts() : void {}
	
	###########
	### GDO ###
	###########
	public function &gdoColumnsCache() : array { return Database::columnsS(self::class); } # Polymorph fix
	public function gdoTableName() : string { return 'gdo_module'; } # Polymorph fix
	public function gdoClassName() : string { return self::class; } # Polymorph fix
	public function gdoRealClassName() : string { return static::class; } # Polymorph fix fix
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('module_id'),
			GDT_Sort::make('module_priority'),
			GDT_Name::make('module_name')->notNull()->unique(),
			GDT_Version::make('module_version')->notNull(),
			GDT_Checkbox::make('module_enabled')->notNull()->initial('0'),
		];
	}
	
	##############
	### Static ###
	##############
	/**
	 * @return self
	 */
	public static function instance() : self
	{
		return ModuleLoader::instance()->getModule(self::getNameS(), true);
	}
	
	private static array $nameCache = [];

	public static function getNameS()
	{
	    if (isset(self::$nameCache[static::class]))
	    {
	        return self::$nameCache[static::class];
	    }
	    self::$nameCache[static::class] = $cache = strtolower(substr(self::gdoShortNameS(), 7));
	    return $cache;
	}
	
	##############
	### Getter ###
	##############
	public function getID() : ?string { return $this->gdoVar('module_id'); }
	public function getName() : ?string { return $this->getModuleName(); }
	public function getVersion() : Version { return $this->gdoValue('module_version'); }
	public function isEnabled() : bool
	{
		if (GDO_DB_ENABLED)
		{
			return !!$this->gdoVar('module_enabled');
		}
		return true;
	}
	public function isInstalled() : bool { return $this->isPersisted(); }
	
	###############
	### Display ###
	###############
	public function render_fs_version() { return $this->version; }
	
	############
	### Href ###
	############
	public function href($methodName, $append='') { return href($this->getName(), $methodName, $append); }
	public function href_install_module() { return href('Admin', 'Install', '&module='.$this->getName()); }
	public function href_configure_module() { return href('Admin', 'Configure', '&module='.$this->getName()); }
	public function href_administrate_module() : ?string { return null; }
	
	##############
	### Helper ###
	##############
	public function canUpdate() { return $this->version !== $this->getVersion()->__toString(); }
	public function canInstall() { return !$this->isPersisted(); }
	
	/**
	 * Filesystem path for a file within this module.
	 * @param string $path
	 * @return string
	 */
	public function filePath($path='') { return rtrim(GDO_PATH, '/') . $this->wwwPath($path, '/'); }
	
	/**
	 * Relative www path for a resource.
	 * @param string $path
	 * @return string
	 */
	public function wwwPath($path='', $webRoot=GDO_WEB_ROOT)
	{
	    return $webRoot . "GDO/{$this->getName()}/{$path}";
	}
	
	/**
	 * Filesystem path for a temp file. Absolute path to the gdo6/temp/{module}/ folder.
	 * @param string $filename appendix filename
	 * @return string the absolute path
	 */
	public function tempPath($path='')
	{
	    $base = Application::$INSTANCE->isUnitTests() ?
	       'temp_test' : 'temp';
	    $path = GDO_PATH . "{$base}/" . $this->getName() .'/' . $path;
	    $dir = Strings::rsubstrTo($path, "/");
	    FileUtil::createDir($dir);
	    return $path;
	}
	
	#################
	### Templates ###
	#################
	public function php($path, array $tVars=null)
	{
	    return GDT_Template::php($this->getName(), $path, $tVars);
	}
	
	public function templateFile($file)
	{
	    return GDT_Template::file($this->getName(), $file);
	}
	
	/**
	 * @return GDT_Template
	 */
	public function templatePHP(string $path, array $tVars=null) : GDT_Template
	{
		switch (Application::$INSTANCE->modeDetected)
		{
			case GDT::RENDER_JSON: return $tVars; # @TODO here is the spot to enable json for genereic templates.
			case GDT::RENDER_HTML:
			default: return GDT_Template::make()->template($this->getName(), $path, $tVars);
		}
	}
	
	public function responsePHP(string $file, array $tVars=null) : GDT
	{
		switch (Application::$INSTANCE->modeDetected)
		{
			case GDT::RENDER_JSON: return GDT_JSON::make()->value(...$tVars);
			case GDT::RENDER_HTML:
			default: return $this->templatePHP($file, $tVars);
		}
	}
	
	############
	### Init ###
	############
	public function __wakeup()
	{
	    $this->inited = false;
	    parent::__wakeup();
	}

	private bool $inited = false;
	
	public function initedModule() : void
	{
		$this->inited = true;
	}
	
	public function isInited() : bool
	{
		return $this->inited;
	}
	
	public function loadLanguage($path) : self
	{
		Trans::addPath($this->filePath($path));
		return $this;
	}
	
	#####################
	### Module Config ###
	#####################
	/**
	 * @var GDT[]
	 */
	private array $configCache;
	
	/**
	 * Helper to get the config var for a module.
	 *  
	 * @param string $moduleName
	 * @param string $key
	 * @param string $default
	 * @return string|NULL
	 */
	public static function config_var(string $moduleName, string $key, string $default = null) : ?string
	{
		if ($module = ModuleLoader::instance()->getModule($moduleName, false, false))
		{
			return $module->getConfigVar($key);
		}
		return null;
	}
	
	/**
	 * Get module configuration hashed and cached.
	 * @return GDT[]
	 */
	public function &buildConfigCache() : array
	{
		if (!isset($this->configCache))
	    {
			$this->configCache = [];
        	foreach ($this->getConfig() as $gdt)
            {
            	if ($gdt->hasName())
            	{
	                $this->configCache[$gdt->getName()] = $gdt; #->gdo($this);
            	}
	        }
	    }
	    return $this->configCache;
	}
	
	public function &getConfigCache()
	{
		if (!isset($this->configCache))
	    {
    	    $this->buildConfigCache();
	    }
	    return $this->configCache;
	}
	
	private function configCacheKey()
	{
	    return $this->getName().'_config_cache';
	}
	
	public function getConfigMemcache()
	{
	    $key = $this->configCacheKey();
	    if (false === ($cache = Cache::get($key)))
	    {
	        $cache = $this->buildConfigCache();
	        Cache::set($key, $cache);
	    }
	    return $cache;
	}
	
	/**
	 * @param GDT
	 */
	public function getConfigColumn($key, $throwError=true)
	{
		if (!isset($this->configCache))
		{
			$this->buildConfigCache();
		}
	    if (isset($this->configCache[$key]))
	    {
	        return $this->configCache[$key];
	    }
	    if ($throwError)
	    {
	        throw new GDO_Error('err_unknown_config', [$this->renderName(), html($key)]);
	    }
	}
	
	public function getConfigVar($key)
	{
	    if ($gdt = $this->getConfigColumn($key))
	    {
	        return $gdt->var;
	    }
	}
	
	public function getConfigValue(string $key)
	{
	    if ($gdt = $this->getConfigColumn($key))
	    {
	        return $gdt->getValue();
	    }
	}
	
	public function saveConfigVar($key, $var)
	{
	    $gdt = $this->getConfigColumn($key);
	    GDO_ModuleVar::createModuleVar($this, $gdt->initial($var));
	    Cache::remove('gdo_modules');
	}
	
	public function saveConfigValue($key, $value)
	{
	    GDO_ModuleVar::createModuleVar($this, $this->getConfigColumn($key)->initialValue($value));
	    Cache::remove('gdo_modules');
	}
	
	public function removeConfigVar($key)
	{
	    if ($gdt = $this->getConfigColumn($key))
	    {
	        $gdt->initial(null);
	        GDO_ModuleVar::removeModuleVar($this, $key);
	    }
	}
	
	public function increaseConfigVar($key, $by=1)
	{
	    $value = $this->getConfigValue($key);
	    return $this->saveConfigVar($key, $value + 1);
	}
	
	###################
	### User config ###
	###################
	/**
	 * Config that the user cannot change.
	 * @return GDT[]
	 */
	public function getUserConfig() {}
	
	/**
	 * User changeable settings.
	 * @return GDT[]
	 */
	public function getUserSettings() {}
	
	/**
	 * User changeable settings in a blob table. For e.g. signature.
	 * @return GDT[]
	 */
	public function getUserSettingBlobs() {}
	
	####################
	### Settings API ###
	####################
	public function setting($key) : GDT
	{
	    return $this->userSetting(GDO_User::current(), $key);
	}
	
	/**
	 * 
	 * @param GDO_User $user
	 * @param string $key
	 * @return GDT
	 */
	public function userSetting(GDO_User $user, $key) : GDT
	{
	    if ($gdt = $this->getSetting($key))
	    {
    	    $settings = $this->loadUserSettings($user);
    	    if ($acl = @$this->userConfigCacheACL[$key])
    	    {
    	    	if ($def = $this->getACLDefaultsFor($key))
    	    	{
    	    		$acl->initialACL($def[0], $def[1], $def[2]);
    	    	}
    	    }
    	    $gdt->setGDOData($settings);
    	    return $gdt;
	    }
	}
	
	public function settingVar($key)
	{
	    return $this->userSettingVar(GDO_User::current(), $key);
	}
	
	public function settingValue($key)
	{
	    return $this->userSettingValue(GDO_User::current(), $key);
	}
	
	public function userSettingVar(GDO_User $user, string $key) : ?string
	{
	    return $this->userSetting($user, $key)->var;
	}
	
	public function userSettingValue(GDO_User $user, $key)
	{
	    $gdt = $this->userSetting($user, $key);
	    return $gdt->getValue();
	}
	
	public function saveSetting($key, $var)
	{
	    return self::saveUserSetting(GDO_User::current(), $key, $var);
	}
	
	public function saveUserSetting(GDO_User $user, $key, $var)
	{
	    $gdt = $this->getSetting($key);
	    if (!$user->getID())
	    {
	        return $gdt; # @TODO either persist the user on save setting or else?
	    }
	    $data = [
	        'uset_user' => $user->getID(),
	        'uset_name' => $key,
	        'uset_value' => $var,
	    ];
	    $entry = ($gdt instanceof GDT_Text) ?
	       GDO_UserSettingBlob::blank($data) :
	       GDO_UserSetting::blank($data);
	    $entry->replace();
	    $user->tempUnset('gdo_setting');
	    $user->recache();
	    return $gdt;
	}
	
	public function increaseSetting($key, $by=1)
	{
	    return $this->increaseUserSetting(GDO_User::current(), $key, $by);
	}
	
	public function increaseUserSetting(GDO_User $user, $key, $by=1)
	{
	    return $this->saveUserSetting(
	        $user, $key, $this->userSettingVar($user, $key) + $by);
	}
	
	# Cache
	
	/**
	 * @var GDT[]
	 */
	private array $userConfigCache;
	
	/**
	 * @var GDT_ACL[]
	 */
	private array $userConfigCacheACL;
	
	/**
	 * @var GDT[]
	 */
	private array $userConfigCacheContainers;
	
	/**
	 * @return GDT[]
	 */
	public function &getSettingsCache() : array
	{
		if (!isset($this->userConfigCache))
	    {
	        $this->buildSettingsCache();
	    }
	    return $this->userConfigCache;
	}
	
	public function &getSettingsCacheContainers() : array
	{
		if (!isset($this->userConfigCache))
		{
			$this->buildSettingsCache();
		}
		return $this->userConfigCacheContainers;
	}
	
	public function hasSetting($key)
	{
	    $this->buildSettingsCache();
	    return isset($this->userConfigCache[$key]);
	}

	private function getSetting($key)
	{
		$this->buildSettingsCache();
		if (isset($this->userConfigCache[$key]))
	    {
    	    return $this->userConfigCache[$key];
	    }
	    else
	    {
	        throw new GDO_Error('err_unknown_user_setting', [$this->renderName(), html($key)]);
	    }
	}
	
	public function getSettingACL(string $name) : ?GDT_ACL
	{
		return isset($this->userConfigCacheACL[$name]) ? $this->userConfigCacheACL[$name] : null;
	}
	
	public function &buildSettingsCache()
	{
	    if (!isset($this->userConfigCache))
	    {
	    	$this->userConfigCache = [];
	    	$this->userConfigCacheACL = [];
	    	$this->userConfigCacheContainers = [];
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
    	    	$this->userConfigCacheContainers['div_set'] = GDT_Divider::make('div_sett')->label('mt_account_settings');
    	    	$this->_buildSettingsCacheB($configs, true);
    	    }
    	    if ($config = $this->getUserConfig())
    	    {
    	    	$this->userConfigCacheContainers['div_conf'] = GDT_Divider::make('div_conf')->label('mt_account_config');
    	    	$this->_buildSettingsCacheB($config, false);
    	    }
	    }
	    return $this->userConfigCache;
	}
	
	private function _buildSettingsCacheB(array $gdts, bool $writeable) : void
	{
		$first = true;
		foreach ($gdts as $gdt)
		{
			if (!$first)
			{
				$this->userConfigCacheContainers[] = GDT_HR::make();
			}
			else
			{
				$first = false;
			}
			$gdt->writeable($writeable);
			$this->_buildSettingsCacheC($gdt);
		}
	}
	
	private function _buildSettingsCacheC(GDT $gdt) : void
	{
		$name = $gdt->getName();
		$this->userConfigCache[$name] = $gdt;
		$this->userConfigCacheContainers[$name] = $gdt;
		# If it's a saved field, build the ACL container.
		if ($gdt->isACLCapable())
		{
			$this->_buildSettingsCacheD($gdt);
		}
	}
	
	private function _buildSettingsCacheD(GDT $gdt) : void
	{
		$name = $gdt->getName();
		$acl = GDT_ACL::make("acl_{$name}");
		$this->userConfigCacheACL[$name] = $acl;
		
		$level = $acl->aclLevel;
		$relation = $acl->aclRelation;
		$permission = $acl->aclPermission;
		
		# Each var results in 3 GDT ACL vars in config cache.
		$this->userConfigCache[$level->name] = $level;
		$this->userConfigCache[$relation->name] = $relation;
		$this->userConfigCache[$permission->name] = $permission;
		
		# we add the GDT + a container with 3 acl fields to the container cache.
		$cont = GDT_Container::make()->horizontal();
		$cont->addFields($relation, $level, $permission);
		$this->userConfigCacheContainers[] = $cont;
	}
	
	private function loadUserSettings(GDO_User $user)
	{
	    if (null === ($settings = $user->tempGet('gdo_setting')))
	    {
	        $settings = self::loadUserSettingsB($user);
	        $user->tempSet('gdo_setting', $settings);
	        $user->recache();
	    }
	    return $settings;
	}
	
	private function loadUserSettingsB(GDO_User $user)
	{
	    if (!$user->isPersisted())
	    {
	        return [];
	    }
	    return array_merge(
	        GDO_UserSetting::table()->select('uset_name, uset_value')->
	           where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair(),
	        GDO_UserSettingBlob::table()->select('uset_name, uset_value')->
	           where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair()
	    );
	}
	
	###########
	### ACL ###
	###########
	protected function getACLDefaults() : ?array
	{
		return null;
	}
	
	private function getACLDefaultsFor(string $key) : ?array
	{
		if ($defaults = $this->getACLDefaults())
		{
			if (isset($defaults[$key]))
			{
				return $defaults[$key];
			}
		}
		return null;
	}
	
	private function getACLDefaultRelation(string $key) : string
	{
		if ($defaults = $this->getACLDefaultsFor($key))
		{
			return $defaults[0];
		}
		return 'acl_noone';
	}
	
	private function getACLDefaultLevel(string $key) : int
	{
		if ($defaults = $this->getACLDefaultsFor($key))
		{
			return $defaults[1];
		}
		return 0;
	}
	
	private function getACLDefaultPermission(string $key) : ?string
	{
		if ($defaults = $this->getACLDefaultsFor($key))
		{
			return $defaults[2];
		}
		return null;
	}
	
	##############
	### Method ###
	##############
	/**
	 * @param string $methodName
	 * @return Method
	 */
	public function getMethod($methodName)
	{
	    $methods = $this->getMethods(false);
	    foreach ($methods as $method)
	    {
	        if (strcasecmp($methodName, $method->gdoShortName()) === 0)
	        {
	            return $method;
	        }
	    }
	}
	
	/**
	 * Get a method by name. Case insensitive.
	 * @param string $methodName
	 * @return Method
	 */
	public function getMethodByName($methodName)
	{
	    $files = scandir($this->filePath('Method'));
	    foreach ($files as $file)
	    {
	        $file = substr($file, 0, -4);
	        if (strcasecmp($methodName, $file) === 0)
	        {
	            $className = "\\GDO\\{$this->getName()}\\Method\\{$file}";
	            $method = call_user_func([$className, 'make']);
	            return $method;
	        }
	    }
	}
	
	public function getMethodNames($withPermission=true)
	{
	    $methods = $this->getMethods($withPermission);
	    return array_map(function(Method $method) {
	        return $method->gdoShortName();
	    }, $methods);
	}
	
	/**
	 * @param boolean $withPermission
	 * @return Method[]
	 */
	public function getMethods($withPermission=true) : array
	{
		$path =  $this->filePath('Method');
		if (!FileUtil::isDir($path))
		{
			return [];
		}
	    $methods = scandir($path);
	    $methods = array_map(function($file) {
	        return substr($file, 0, -4);
	    }, $methods);
	    $methods = array_filter($methods, function($file) {
            return !!$file;
        });
        $methods = array_map(function($file) {
            $className = "\\GDO\\{$this->getName()}\\Method\\{$file}";
            return call_user_func([$className, 'make']);
        }, $methods);
        if ($withPermission)
        {
            $methods = array_filter($methods, function(Method $method) {
				return $method->hasUserPermission(GDO_User::current());
			});
        }
        return $methods;
	}
	
	##############
	### Assets ###
	##############
	
	/**
	 * nocache appendix
	 * @var string
	 */
	public static string $_NC = '';
	
	/**
	 * Get the ".min" file suffix in case we want minification.
	 */
	public function cfgMinAppend() : string
	{
		$mode = self::config_var('Javascript', 'minify_js', 'no');
		return $mode === 'no' ? '' : '.min';
	}
	
	/**
	 * Get the cache poisoner.
	 * Base is gdo revision string.
	 * Additionally a cache clear triggers an increase of the assets version.
	 * @return string
	 */
	public function nocacheVersion()
	{
	    if (!self::$_NC)
	    {
	        $v = Module_Core::GDO_REVISION;
	        $av = Module_Core::instance()->cfgAssetVersion();
	        self::$_NC = "_v={$v}&_av={$av}";
	    }
        return self::$_NC;
	}
	
	public function addBowerJS($path)
	{
	    return $this->addJS('bower_components/'.$path);
	}
	
	public function addJS($path)
	{
	    return Javascript::addJS(
	        $this->wwwPath($path . '?' . $this->nocacheVersion()));
	}
	
	public function addBowerCSS($path)
	{
	    return $this->addCSS('bower_components/'.$path);
	}
	
	public function addCSS($path)
	{
	    return Website::addCSS($this->wwwPath($path.'?'.$this->nocacheVersion()));
	}

	public function prefetch($path, $type)
	{
	    $v = $this->nocacheVersion();
	    $href = $this->wwwPath($path.'?'.$v);
	    Website::addPrefetch($href, $type);
	}
	
	#############
	### Error ###
	#############
	protected function errorSystemDependency(string $key, array $args = null) : bool
	{
		$msg = t($key, $args);
		return $this->error('err_system_dependency', [$msg]);
	}
	
	public function error(string $key, array $args=null) : bool
	{
		$gdt = GDT_Error::make()->text($key, $args)->titleRaw($this->renderName());
		Application::$INSTANCE->setResponseCode(409);
		Logger::logError(ten($key, $args));
		GDT_Page::instance()->topResponse()->addField($gdt);
		return false;
	}
	
}
