<?php
namespace GDO\Install;

use GDO\UI\GDT_Link;
use GDO\UI\GDT_Divider;
use GDO\Core\GDT_Enum;
use GDO\Core\GDT_Select;
use GDO\Form\GDT_Hidden;
use GDO\Util\Strings;
use GDO\Date\Time;
use GDO\Core\GDT_Checkbox;
use GDO\Util\Random;
use GDO\Core\GDT_Int;
use GDO\Net\GDT_Port;
use GDO\Core\Application;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;
use GDO\Core\Logger;
use GDO\Core\GDT_UInt;

/**
 * Configuration helper during install wizard.
 * Holds a set of method names for the steps
 * Autoconfigures GDO for when no config exists.
 * Holds fields for a configuration form.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class Config
{
	####################
	### Method Steps ###
	####################
	public static function hrefStep(int $step) : string { return $_SERVER['SCRIPT_NAME'] . '?step=' . $step; }
	public static function linkStep(int $step) : string { return self::linkStepGDT($step)->render(); }
	public static function linkStepGDT(int $step) : GDT_Link { return GDT_Link::make("step$step")->href(self::hrefStep($step))->label("install_title_$step"); }
	public static function steps() : array
	{
		return [
			'Welcome',
			'SystemTest',
			'Configure',
			'InstallModules',
			'InstallCronjob',
			'InstallAdmins',
			'InstallJavascript',
			'ImportBackup',
		    'CopyHTAccess',
			'Security',
		];
	}
	
	#############################
	### Config File Generator ###
	#############################
	private static function detectServerSoftware() : string
	{
	    if (!isset($_SERVER['SERVER_SOFTWARE']))
	    {
	        return 'none';
	    }
	    
		$software = $_SERVER['SERVER_SOFTWARE'];
		if (stripos($software, 'Apache') !== false)
		{
			if (strpos($software, '2.4') !== false)
			{
				return 'apache2.4';
			}
			if (strpos($software, '2.2') !== false)
			{
				return 'apache2.2';
			}
			return 'apache2.4';
		}
		if (stripos($software, 'nginx') !== false)
		{
			return 'nginx';
		}
		return 'other';
	}
	
	public static function configure() : void
	{
		# Site
		if (!defined('GDO_SITENAME')) define('GDO_SITENAME', 'GDOv7');
		if (!defined('GDO_SITECREATED')) define('GDO_SITECREATED', Time::getDate(microtime(true)));
		if (!defined('GDO_LANGUAGE')) define('GDO_LANGUAGE', 'en');
// 		if (!defined('GDO_TIMEZONE')) define('GDO_TIMEZONE', ini_get('date.timezone'));
		if (!defined('GDO_THEMES')) define('GDO_THEMES', '[default]');
		if (!defined('GDO_MODULE')) define('GDO_MODULE', 'Core');
		if (!defined('GDO_METHOD')) define('GDO_METHOD', 'Welcome');
		if (!defined('GDO_SEO_URLS')) define('GDO_SEO_URLS', false);
		if (!defined('GDO_IPC')) define('GDO_IPC', 'none');
		if (!defined('GDO_IPC_DEBUG')) define('GDO_IPC_DEBUG', false);
		if (!defined('GDO_GDT_DEBUG')) define('GDO_GDT_DEBUG', false);
		if (!defined('GDO_JSON_DEBUG')) define('GDO_JSON_DEBUG', false);
		# HTTP
		if (!defined('GDO_DOMAIN')) define('GDO_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
		if (!defined('GDO_SERVER')) define('GDO_SERVER', self::detectServerSoftware());
		if (!defined('GDO_PROTOCOL')) define('GDO_PROTOCOL', @$_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
		if (!defined('GDO_PORT')) define('GDO_PORT', @$_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : (GDO_PROTOCOL === 'https' ? 443 : 80));
		if (!defined('GDO_WEB_ROOT')) define('GDO_WEB_ROOT', Strings::substrTo($_SERVER['SCRIPT_NAME'], 'install/wizard.php'));
		# Files
		if (!defined('GDO_CHMOD')) define('GDO_CHMOD', 0770);
		# Logging
		if (!defined('GDO_LOG_REQUEST')) define('GDO_LOG_REQUEST', false);
// 		if (!defined('GDO_CONSOLE_VERBOSE')) define('GDO_CONSOLE_VERBOSE', false);
		if (!defined('GDO_ERROR_LEVEL')) define('GDO_ERROR_LEVEL', Logger::_DEFAULT);
		if (!defined('GDO_ERROR_STACKTRACE')) define('GDO_ERROR_STACKTRACE', true);
		if (!defined('GDO_ERROR_DIE')) define('GDO_ERROR_DIE', true);
		if (!defined('GDO_ERROR_MAIL')) define('GDO_ERROR_MAIL', false);
		if (!defined('GDO_ERROR_TIMEZONE')) define('GDO_ERROR_TIMEZONE', 'UTC');

		# Database
		if (!defined('GDO_SALT')) define('GDO_SALT', Random::randomKey(16));
		if (!defined('GDO_DB_ENABLED')) define('GDO_DB_ENABLED', true);
		if (!defined('GDO_DB_HOST')) define('GDO_DB_HOST', 'localhost');
		if (!defined('GDO_DB_USER')) define('GDO_DB_USER', '');
		if (!defined('GDO_DB_PASS')) define('GDO_DB_PASS', '');
		if (!defined('GDO_DB_NAME')) define('GDO_DB_NAME', '');
		if (!defined('GDO_DB_DEBUG')) define('GDO_DB_DEBUG', false);
		# Cache
		if (!defined('GDO_FILECACHE')) define('GDO_FILECACHE', false);
		if (!defined('GDO_MEMCACHE')) define('GDO_MEMCACHE', false);
		if (!defined('GDO_MEMCACHE_HOST')) define('GDO_MEMCACHE_HOST', '127.0.0.1');
		if (!defined('GDO_MEMCACHE_PORT')) define('GDO_MEMCACHE_PORT', 61221);
		if (!defined('GDO_MEMCACHE_TTL')) define('GDO_MEMCACHE_TTL', 1800);
		# Cookies
		if (!defined('GDO_SESS_NAME')) define('GDO_SESS_NAME', 'GDO7');
		if (!defined('GDO_SESS_DOMAIN')) define('GDO_SESS_DOMAIN', GDO_DOMAIN);
		if (!defined('GDO_SESS_TIME')) define('GDO_SESS_TIME', Time::ONE_DAY*2);
		if (!defined('GDO_SESS_JS')) define('GDO_SESS_JS', false);
		if (!defined('GDO_SESS_HTTPS')) define('GDO_SESS_HTTPS', Application::$INSTANCE->isTLS());
		if (!defined('GDO_SESS_SAMESITE')) define('GDO_SESS_SAMESITE', 'Lax');
		if (!defined('GDO_SESS_LOCK')) define('GDO_SESS_LOCK', GDO_DB_ENABLED);
		
		# Email
		if (!defined('GDO_ENABLE_EMAIL')) define('GDO_ENABLE_EMAIL', false);
		if (!defined('GDO_BOT_NAME')) define('GDO_BOT_NAME', GDO_SITENAME . ' support');
		if (!defined('GDO_BOT_EMAIL')) define('GDO_BOT_EMAIL', 'support@'.GDO_DOMAIN);
		if (!defined('GDO_ADMIN_EMAIL')) define('GDO_ADMIN_EMAIL', 'administrator@'.GDO_DOMAIN);
		if (!defined('GDO_ERROR_EMAIL')) define('GDO_ERROR_EMAIL', 'administrator@'.GDO_DOMAIN);
		if (!defined('GDO_DEBUG_EMAIL')) define('GDO_DEBUG_EMAIL', true);
	}
	
	public static function fields() : array
	{
		$themes = GDT_Template::themeNames();
		return [
			GDT_Hidden::make('configured')->var('1'),
			
			# Site
			GDT_Divider::make()->label('install_config_section_site'),
			GDT_String::make('sitename')->initialValue(GDO_SITENAME)->max(16)->label('cfg_sitename'),
			GDT_Checkbox::make('seo_urls')->initialValue(GDO_SEO_URLS?true:false),
			GDT_Hidden::make('sitecreated')->var(GDO_SITECREATED),
			GDT_Enum::make('language')->enumValues('en', 'de')->initialValue(GDO_LANGUAGE)->notNull(),
			// 		    GDT_String::make('timezone')->initialValue(GDO_TIMEZONE)->notNull(),
			GDT_Select::make('themes')->multiple()->choices(array_combine($themes, $themes))->notNull()->initialValue(array('default')),
			GDT_String::make('module')->notNull()->initialValue(GDO_MODULE),
			GDT_String::make('method')->notNull()->initialValue(GDO_METHOD),
			GDT_Select::make('ipc')->emptyInitial('select_ipc_mode', '')->choices(['db' => 'Database', 'ipc' => 'IPC', 'none' => 'none'])->initialValue(GDO_IPC),
			GDT_Checkbox::make('ipc_debug')->initialValue(GDO_IPC_DEBUG?true:false),
			GDT_Checkbox::make('gdt_debug')->initialValue(GDO_GDT_DEBUG),
			GDT_Checkbox::make('json_debug')->initialValue(GDO_JSON_DEBUG?true:false),
			# HTTP
			GDT_Divider::make()->label('install_config_section_http'),
			GDT_String::make('domain')->notNull()->initialValue(GDO_DOMAIN),
			GDT_Enum::make('server')->notNull()->enumValues('none', 'apache2.2', 'apache2.4', 'nginx', 'other')->initialValue(GDO_SERVER),
			GDT_Enum::make('protocol')->notNull()->enumValues('http', 'https')->initialValue(GDO_PROTOCOL),
			GDT_String::make('web_root')->notNull()->initialValue(GDO_WEB_ROOT),
			# Files
			GDT_Divider::make()->label('install_config_section_files'),
			GDT_Enum::make('chmod')->enumValues("0700", "0770", "0777")->initial('0'.base_convert(GDO_CHMOD, 10, 8)),
			# Logging
			GDT_Divider::make()->label('install_config_section_logging'),
			GDT_Checkbox::make('log_request')->initialValue(GDO_LOG_REQUEST?true:false),
// 			GDT_Checkbox::make('console_verbose')->initialValue(GDO_CONSOLE_VERBOSE),
			GDT_Hidden::make('error_level')->initialValue(GDO_ERROR_LEVEL),
			GDT_Checkbox::make('error_stacktrace')->initialValue(GDO_ERROR_STACKTRACE?true:false),
			GDT_Checkbox::make('error_die')->initialValue(GDO_ERROR_DIE?true:false),
			GDT_Checkbox::make('error_mail')->initialValue(GDO_ERROR_MAIL?true:false),
			# Database
			GDT_Divider::make()->label('install_config_section_database'),
			GDT_Hidden::make('salt')->initialValue(GDO_SALT),
			GDT_Checkbox::make('db_enabled')->initialValue(GDO_DB_ENABLED?true:false),
			GDT_String::make('db_host')->initialValue(GDO_DB_HOST),
			GDT_String::make('db_user')->initialValue(GDO_DB_USER),
			GDT_String::make('db_pass')->initialValue(GDO_DB_PASS),
			GDT_String::make('db_name')->initialValue(GDO_DB_NAME),
			//			 Text::make('db_prefix')->initialValue(GDO_DB_PREFIX)->notNull(),
			GDT_Checkbox::make('db_debug')->initialValue(GDO_DB_DEBUG),
			# Cache
			GDT_Divider::make()->label('install_config_section_cache'),
			GDT_Checkbox::make('filecache')->initialValue(GDO_FILECACHE),
			GDT_Checkbox::make('memcache')->initialValue(GDO_MEMCACHE),
			GDT_String::make('memcache_host')->initialValue(GDO_MEMCACHE_HOST)->notNull(),
			GDT_Port::make('memcache_port')->initialValue(GDO_MEMCACHE_PORT)->notNull(),
			GDT_Int::make('memcache_ttl')->unsigned()->initialValue(GDO_MEMCACHE_TTL)->notNull(),
			# Cookies
			GDT_Divider::make()->label('install_config_section_cookies'),
			GDT_String::make('sess_name')->ascii()->caseS()->initialValue(GDO_SESS_NAME)->notNull(),
			GDT_Hidden::make('sess_domain')->initialValue(GDO_SESS_DOMAIN),
			GDT_UInt::make('sess_time')->initialValue(GDO_SESS_TIME)->notNull()->min(30),
			GDT_Checkbox::make('sess_js')->initialValue(GDO_SESS_JS),
			GDT_Checkbox::make('sess_https')->initialValue(GDO_SESS_HTTPS),
			GDT_Checkbox::make('sess_lock')->initialValue(GDO_SESS_LOCK),
			GDT_Checkbox::make('sess_samesite')->initialValue(GDO_SESS_SAMESITE),
			# Email
			GDT_Divider::make()->label('install_config_section_email'),
			GDT_Checkbox::make('enable_email')->initialValue(GDO_ENABLE_EMAIL),
			GDT_String::make('bot_name')->notNull()->initialValue(GDO_BOT_NAME)->label('bot_name'),
			GDT_String::make('bot_email')->notNull()->initialValue(GDO_BOT_EMAIL)->label('bot_mail'),
			GDT_String::make('admin_email')->notNull()->initialValue(GDO_ADMIN_EMAIL)->label('admin_mail'),
			GDT_String::make('error_email')->notNull()->initialValue(GDO_ERROR_EMAIL)->label('error_mail'),
			GDT_Checkbox::make('debug_email')->initialValue(GDO_DEBUG_EMAIL),
		];
	}
	
}
