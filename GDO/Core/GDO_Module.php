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
use GDO\UI\GDT_Link;
use GDO\Table\GDT_Sort;

/**
 * GDO base module class.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 1.0.0
 */
class GDO_Module extends GDO
{
	################
	### Override ###
	################
	public int $priority = 50;
	public string $version = "7.0.0";
	public string $license = "GDOv7-LICENSE";
	public string $author = "Christian B. <gizmore@wechall.net>";
	
	public function gdoCached() : bool { return false; }
	public function memCached() : bool { return false; }
	public function defaultEnabled() : bool { return !$this->isSiteModule(); }
	public function isCoreModule() : bool { return false; }
	public function isSiteModule() : bool { return false; }
	public function isInstallable() : bool { return true; }
	
	/**
	 * A list of required dependencies.
	 * Override this. Please add Core to your dependencies!
	 * @return string[]
	 */
	public function getDependencies() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
    /**
     * A list of optional modules that enhance this one.
     * Override this.
	 * @return string[]
	 */
	public function getFriendencies() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
    /**
	 * Skip these folders in unit tests using strpos.
	 * 
	 * @see Module_Tests
	 * @return string[]
	 */
	public function thirdPartyFolders() : array {}
	
// 	/**
// 	 * Get all module dependencies as moduleName.
// 	 * @return string[]
// 	 */
// 	public function dependencies()
// 	{
// 	    $coreDeps = $this->gdoDependencies();
// 	    if ($deps = $this->getDependencies())
// 	    {
// 	        return array_unique(array_merge($coreDeps, $deps));
// 	    }
// 	    else
// 	    {
// 	        return $coreDeps;
// 	    }
// 	}
	
	/**
	 * Provided theme name in module /thm/$themeName/ folder.
	 * @return string $themeName
	 */
	public function getTheme() : ?string { return null; }
	
	/**
	 * GDO classes to install.
	 * @return string[]
	 */
	public function getClasses() : array { return GDT::EMPTY_ARRAY; }
	
	/**
	 * Module config GDTs
	 * @return GDT[]
	 */
	public function getConfig() : array { return GDT::EMPTY_ARRAY; }
	
	############
	### Info ###
	############
	/**
	 * Translated module name.
	 */
	public function displayName() : string
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
	
	public function getModuleLicenseFilenames() : array
	{
	    return [
	        'LICENSE',
	    ];
	}
	
	/**
	 * Print license information.
	 * @TODO move to module gdo6-licenses
	 * @return string
	 */
	public function getModuleLicense() : string
	{
	    $all = '';
	    
	    $files = $this->getModuleLicenseFilenames();
	    
	    $div = '<hr/>';
	    
	    if ($descr = $this->getModuleDescription())
	    {
	    	$all .= "$descr\n$div";
	    	if ($files)
	    	{
	    		$gdo = 0; # gdo licenses
	    		foreach ($files as $file)
	    		{
	    			if ($this->filePath('LICENSE') === $this->filePath($file))
	    			{
	    				$gdo = 1;
	    			}
	    		}
	    		
	    		$count = count($files) - $gdo;
	    		if ($count)
	    		{
		    		$all .= "$count third-party-licenses involved:";
		    		$all .= "\n$div";
	    		}
	    	}
	    }
	    
	    if ($files)
	    {
	        foreach ($files as $i => $filename)
	        {
	            if ($i > 0)
	            {
	            	$all .= "\n$div";
	            }

	            $all .= GDT_Link::make()->
	            	labelRaw(Strings::substrFrom($filename, GDO_WEB_ROOT))->
	            	href($this->wwwPath($filename))->
	            	renderCell();
	            
       	        $filename = $this->filePath($filename);
        	    if (FileUtil::isFile($filename))
        	    {
        	        $all .= file_get_contents($filename);
        	    }
	        }
	    }
	    else
	    {
	        $all .= 'UNLICENSED / PROPERITARY';
	    }
        return $all;
	}

// 	public function displayModuleDescription() : string { return html($this->getModuleDescription()); }
	
	/**
	 * Module description is fetched from README.md by default.
	 * @return string
	 */
	public function getModuleDescription() : ?string
	{
		if ($readme = @file_get_contents($this->filePath('README.md')))
		{
			$matches = null;
			if (preg_match("/^#.*[\\r\\n]+(.*)[\\r\\n]/iD", $readme, $matches))
			{
				return $matches[1];
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
	public function onInit() : void {}
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
	public function href_administrate_module() {}
	
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
	    $base = Application::instance()->isUnitTests() ?
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
	 * @param string $file
	 * @param array $tVars
	 * @return GDT_Template
	 */
	public function templatePHP(string $path, array $tVars=null) : GDT_Template
	{
		switch (Application::instance()->getFormat())
		{
			case 'json': return $tVars; # @TODO here is the spot to enable json for genereic templates.
			case 'html':
			default: return GDT_Template::make()->template($this->getName(), $path, $tVars);
		}
	}
	
	public function responsePHP(string $file, array $tVars=null) : GDT
	{
		switch (Application::instance()->getFormat())
		{
			case 'json': return GDT_Array::makeWith(...$tVars);
			case 'html':
			default: return $this->templatePHP($file, $tVars);
		}
	}
	
// 	public function error(string $key, array $args=null, int $code=GDO_Exception::DEFAULT_ERROR_CODE, bool $log=true) : GDT_Error
// 	{
// 		if ($log)
// 		{
// 			Logger::logError(t($key, $args));
// 		}
// 		return GDT_Error::make()->text($key, $args);
// 	}
	
// 	public function message(string $key, array $args=null, bool $log=true) : GDT_Message
// 	{
// 		if ($log)
// 		{
// 			Logger::logMessage(ten($key, $args));
// 		}
// 		return GDT_Success::make()->text($key, $args);
// 	}
	
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
	        throw new GDO_Error('err_unknown_config', [$this->displayName(), html($key)]);
	    }
	}
	
	public function getConfigVar($key)
	{
	    if ($gdt = $this->getConfigColumn($key))
	    {
	        return $gdt->var;
	    }
	}
	
	public function getConfigValue($key)
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
	 * Special URL for settings.
	 */
	public function getUserSettingsURL() {}
	
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
	public function setting($key)
	{
	    return $this->userSetting(GDO_User::current(), $key);
	}
	
	/**
	 * 
	 * @param GDO_User $user
	 * @param string $key
	 * @return GDT
	 */
	public function userSetting(GDO_User $user, $key)
	{
	    if ($gdt = $this->getSetting($key))
	    {
	    	$gdt->gdo($user);
    	    $settings = $this->loadUserSettings($user);
    	    $var = isset($settings[$key]) ? $settings[$key] : $gdt->initial;
   	        return $gdt->initial($var);
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
	
	public function userSettingVar(GDO_User $user, $key)
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
	        return $gdt;
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
	private $userConfigCache = null;
	
	public function &getSettingsCache()
	{
	    if ($this->userConfigCache === null)
	    {
	        $this->buildSettingsCache();
	    }
	    return $this->userConfigCache;
	}
	
	public function hasSetting($key)
	{
	    $this->buildSettingsCache();
	    return isset($this->userConfigCache[$key]);
	}

	private function getSetting($key)
	{
	    if (isset($this->userConfigCache[$key]))
	    {
    	    return $this->userConfigCache[$key];
	    }
	    else
	    {
	        throw new GDO_Error('err_unknown_user_setting', [$this->displayName(), html($key)]);
	    }
	}
	
	public function &buildSettingsCache()
	{
	    if ($this->userConfigCache === null)
	    {
    	    $this->userConfigCache = [];
    	    if ($config = $this->getUserConfig())
    	    {
    	        foreach ($config as $gdt)
    	        {
    	            $gdt->editable(false);
    	            $this->userConfigCache[$gdt->name] = $gdt;
    	        }
    	    }
    	    if ($config = $this->getUserSettings())
    	    {
    	        foreach ($config as $gdt)
    	        {
    	            $this->userConfigCache[$gdt->name] = $gdt;
    	        }
    	    }
    	    if ($config = $this->getUserSettingBlobs())
    	    {
    	        foreach ($config as $gdt)
    	        {
    	            $this->userConfigCache[$gdt->name] = $gdt;
    	        }
    	    }
	    }
	    return $this->userConfigCache;
	}
	
	private function loadUserSettings(GDO_User $user)
	{
	    if (null === ($settings = $user->tempGet('gdo_setting')))
	    {
	        $settings = self::loadUserSettingsB($user);
	        $user->tempSet('gdo_setting', $settings);
// 	        $user->recache();
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
	public function getMethods($withPermission=true)
	{
	    $methods = scandir($this->filePath('Method'));
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
//                 try
//                 {
                    return $method->hasUserPermission(GDO_User::current());
//                 }
//                 catch (\Throwable $ex)
//                 {
//                     return false;
//                 }
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
	
	public static function minAppend()
	{
		if (self::config_var('Javascript', 'minify_js', 'no') !== 'no')
		{
			return '.min';
		}
		return '';
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
	
	######################
	### Default Method ###
	######################
	/**
	 * Override this in case your module has a special default method.
	 * The default case is that all modules reference to your config.php - GDO_MODULE + GDO_METHOD
	 */
	public function getDefaultMethod() : Method
	{
		$klass = sprintf('\\GDO\\%s\\Method\\%s', GDO_MODULE, GDO_METHOD);
		return call_user_func([$klass, 'make']);
	}
	
}
