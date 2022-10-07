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
use GDO\Net\GDT_Port;
use GDO\Core\Application;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;
use GDO\Core\Logger;
use GDO\Core\GDT_UInt;
use GDO\Core\GDT_TinyInt;
use GDO\Core\GDT_EnumNoI18n;
use GDO\Core\GDO;
use GDO\Core\GDT_Path;

/**
 * Configuration helper during install wizard.
 * Holds a set of method names for the steps
 * Autoconfigures GDO for when no config exists.
 * Holds fields for a configuration form.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class Config
{
	####################
	### Method Steps ###
	####################
	public static function hrefStep(int $step) : string
	{
		return $_SERVER['SCRIPT_NAME'] . '?step=' . $step;
	}
	
	public static function linkStep(int $step) : string
	{
		return self::linkStepGDT($step)->render();
	}
	
	public static function linkStepGDT(int $step) : GDT_Link
	{
		return GDT_Link::make("step$step")->href(self::hrefStep($step))->label("install_title_$step");
	}
	
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
	/**
	 * Auto-configure GDOv7.
	 */
	public static function configure() : void
	{
		# Site
		deff('GDO_SITENAME', 'GDOv7');
		deff('GDO_ENV', 'dev');
		deff('GDO_SITECREATED', Time::getDate());
		deff('GDO_LANGUAGE', 'en');
		deff('GDO_TIMEZONE', ini_get('date.timezone')); # @TODO use the full timezone name here for the default timezone in module date.
		deff('GDO_THEMES', 'classic,default');
		deff('GDO_MODULE', 'Core');
		deff('GDO_METHOD', 'Welcome');
		deff('GDO_SEO_URLS', false);
		deff('GDO_IPC', 'none');
		deff('GDO_IPC_DEBUG', false);
		deff('GDO_GDT_DEBUG', 0);
		deff('GDO_JSON_DEBUG', false);
		# HTTP
		deff('GDO_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
		deff('GDO_SERVER', self::detectServerSoftware());
		deff('GDO_PROTOCOL', @$_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
		deff('GDO_PORT', @$_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : (GDO_PROTOCOL === 'https' ? 443 : 80));
		deff('GDO_WEB_ROOT', Strings::substrTo($_SERVER['SCRIPT_NAME'], 'install/wizard.php'));
		deff('GDO_FORCE_SSL', false);
		# Files
		deff('GDO_CHMOD', 0770);
		deff('GDO_FILES_DIR', 'files');
		# Logging
		deff('GDO_LOG_REQUEST', false);
		deff('GDO_ERROR_LEVEL', Logger::_DEFAULT);
		deff('GDO_ERROR_STACKTRACE', true);
		deff('GDO_ERROR_DIE', true);
		deff('GDO_ERROR_MAIL', false);
		deff('GDO_ERROR_TIMEZONE', 'UTC');
		# Database
		deff('GDO_SALT', Random::randomKey(16));
		deff('GDO_DB_ENABLED', true);
		deff('GDO_DB_HOST', 'localhost');
		deff('GDO_DB_PORT',  3306);
		deff('GDO_DB_USER', '');
		deff('GDO_DB_PASS', '');
		deff('GDO_DB_NAME', '');
		deff('GDO_DB_ENGINE', GDO::INNODB);
		deff('GDO_DB_DEBUG', 0);
		# Cache
		deff('GDO_CACHE_DEBUG', 0);
		deff('GDO_FILECACHE', true);
		deff('GDO_MEMCACHE', 2);
		deff('GDO_MEMCACHE_HOST', '127.0.0.1');
		deff('GDO_MEMCACHE_PORT', 61221);
		deff('GDO_MEMCACHE_TTL', 1800);
		# Cookies
		deff('GDO_SESS_NAME', 'GDO7');
		deff('GDO_SESS_DOMAIN', GDO_DOMAIN);
		deff('GDO_SESS_TIME', Time::ONE_DAY*7);
		deff('GDO_SESS_JS', false);
		deff('GDO_SESS_HTTPS', Application::$INSTANCE->isTLS());
		deff('GDO_SESS_SAMESITE', 'lax');
		deff('GDO_SESS_LOCK', GDO_DB_ENABLED);
		# Email
		deff('GDO_ENABLE_EMAIL', false);
		deff('GDO_BOT_NAME', GDO_SITENAME . ' Support System');
		deff('GDO_BOT_EMAIL', 'support@'.GDO_DOMAIN);
		deff('GDO_ADMIN_EMAIL', 'administrator@'.GDO_DOMAIN);
		deff('GDO_ERROR_EMAIL', 'errors@'.GDO_DOMAIN);
		deff('GDO_DEBUG_EMAIL', true);
	}
	
	/**
	 * GDT for the config file.
	 */
	public static function fields() : array
	{
		$themes = GDT_Template::themeNames();
		return [
			GDT_Hidden::make('configured')->var('1'),
			
			# Site
			GDT_Divider::make()->label('install_config_section_site'),
			GDT_String::make('sitename')->initialValue(GDO_SITENAME)->max(16)->label('cfg_sitename'),
			GDT_EnumNoI18n::make('env')->initial('dev')->enumValues('dev', 'tes', 'pro'),
			GDT_Checkbox::make('seo_urls')->initialValue(!!GDO_SEO_URLS),
			GDT_Hidden::make('sitecreated')->var(GDO_SITECREATED),
			GDT_Enum::make('language')->enumValues('en','de','it','fr')->initialValue(GDO_LANGUAGE)->notNull(),
			GDT_String::make('timezone')->initialValue(GDO_TIMEZONE)->notNull(),
			GDT_Select::make('themes')->multiple()->choices(array_combine($themes, $themes))->notNull()->initialValue(array('default')),
			GDT_String::make('module')->notNull()->initialValue(GDO_MODULE),
			GDT_String::make('method')->notNull()->initialValue(GDO_METHOD),
			GDT_Select::make('ipc')->emptyInitial('select_ipc_mode', '')->choices(['db' => 'Database', 'ipc' => 'IPC', 'none' => 'none'])->initialValue(GDO_IPC),
			GDT_Checkbox::make('ipc_debug')->initialValue(!!GDO_IPC_DEBUG),
			GDT_TinyInt::make('gdt_debug')->unsigned()->initialValue((int)GDO_GDT_DEBUG)->min(0)->max(2),
			GDT_Checkbox::make('json_debug')->initialValue(!!GDO_JSON_DEBUG),
			# HTTP
			GDT_Divider::make()->label('install_config_section_http'),
			GDT_Enum::make('server')->notNull()->enumValues('none', 'apache2.2', 'apache2.4', 'nginx', 'other')->initialValue(GDO_SERVER),
			GDT_String::make('domain')->notNull()->initialValue(GDO_DOMAIN),
			GDT_String::make('web_root')->notNull()->initialValue(GDO_WEB_ROOT),
			GDT_Enum::make('protocol')->notNull()->enumValues('http', 'https')->initialValue(GDO_PROTOCOL),
			GDT_Checkbox::make('force_ssl')->initial('0'),
			# Files
			GDT_Divider::make()->label('install_config_section_files'),
			GDT_Path::make('files_dir')->label('files_dir')->initial(GDO_FILES_DIR),
			GDT_Enum::make('chmod')->enumValues("0700", "0770", "0777")->initial('0'.base_convert(GDO_CHMOD, 10, 8)),
			# Logging
			GDT_Divider::make()->label('install_config_section_logging'),
			GDT_Checkbox::make('log_request')->initialValue(!!GDO_LOG_REQUEST),
			GDT_Hidden::make('error_level')->initialValue((int)GDO_ERROR_LEVEL),
			GDT_Checkbox::make('error_stacktrace')->initialValue(!!GDO_ERROR_STACKTRACE),
			GDT_Checkbox::make('error_die')->initialValue(!!GDO_ERROR_DIE),
			GDT_Checkbox::make('error_mail')->initialValue(!!GDO_ERROR_MAIL),
			# Database
			GDT_Divider::make()->label('install_config_section_database'),
			GDT_Hidden::make('salt')->initialValue(GDO_SALT),
			GDT_Checkbox::make('db_enabled')->initialValue(!!GDO_DB_ENABLED),
			GDT_String::make('db_host')->initialValue(GDO_DB_HOST),
			GDT_Port::make('db_port')->initialValue((int)GDO_DB_PORT),
			GDT_String::make('db_user')->initialValue(GDO_DB_USER),
			GDT_String::make('db_pass')->initialValue(GDO_DB_PASS),
			GDT_String::make('db_name')->initialValue(GDO_DB_NAME),
			GDT_EnumNoI18n::make('db_engine')->initial(GDO_DB_ENGINE)->enumValues(GDO::INNODB, GDO::MYISAM),
			GDT_TinyInt::make('db_debug')->unsigned()->initialValue((int)GDO_DB_DEBUG)->min(0)->max(2),
			# Cache
			GDT_Divider::make()->label('install_config_section_cache'),
			GDT_UInt::make('cache_debug')->initialValue((int)GDO_CACHE_DEBUG)->min(0)->max(2),
			GDT_Checkbox::make('filecache')->initialValue(!!GDO_FILECACHE),
			GDT_TinyInt::make('memcache')->unsigned()->min(0)->max(2)->initialValue((int)GDO_MEMCACHE),
			GDT_String::make('memcache_host')->initialValue(GDO_MEMCACHE_HOST)->notNull(),
			GDT_Port::make('memcache_port')->initialValue((int)GDO_MEMCACHE_PORT)->notNull(),
			GDT_UInt::make('memcache_ttl')->unsigned()->initialValue((int)GDO_MEMCACHE_TTL)->notNull(),
			# Cookies
			GDT_Divider::make()->label('install_config_section_cookies'),
			GDT_String::make('sess_name')->ascii()->caseS()->initialValue(GDO_SESS_NAME)->notNull(),
			GDT_Hidden::make('sess_domain')->initialValue(GDO_SESS_DOMAIN),
			GDT_UInt::make('sess_time')->initialValue((int)GDO_SESS_TIME)->notNull()->min(30),
			GDT_Checkbox::make('sess_js')->initialValue(!!GDO_SESS_JS),
			GDT_Checkbox::make('sess_https')->initialValue(!!GDO_SESS_HTTPS),
			GDT_Checkbox::make('sess_lock')->initialValue(!!GDO_SESS_LOCK),
			GDT_EnumNoI18n::make('sess_samesite')->enumValues('lax', 'none', 'strict')->initialValue(GDO_SESS_SAMESITE),
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
	
}
