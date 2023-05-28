<?php
declare(strict_types=1);
namespace GDO\Install;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Enum;
use GDO\Core\GDT_EnumNoI18n;
use GDO\Core\GDT_Int;
use GDO\Core\GDT_Path;
use GDO\Core\GDT_Select;
use GDO\Core\GDT_String;
use GDO\Core\GDT_UInt;
use GDO\Core\Logger;
use GDO\Date\Time;
use GDO\Form\GDT_Hidden;
use GDO\Language\GDO_Language;
use GDO\Net\GDT_Port;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Link;
use GDO\Util\Random;
use GDO\Util\Strings;

/**
 * Configuration helper during install wizard.
 * Holds a set of method names for the steps.
 * Autoconfigures GDO for when no config exists.
 * Holds fields for a configuration form.
 * Is rendering the final protected/config.php via tpl/config.php.
 *
 * @TODO: reduce defined config.php variables by moving them to module configs, example mail and mailer?
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
class Config
{

	####################
	### Method Steps ###
	####################
	public static function linkStep(int $step): string
	{
		return self::linkStepGDT($step)->render();
	}

	public static function linkStepGDT(int $step): GDT_Link
	{
		return GDT_Link::make("step{$step}")->href(self::hrefStep($step))->label("install_title_$step");
	}

	public static function hrefStep(int $step): string
	{
		return $_SERVER['SCRIPT_NAME'] . '?step=' . $step;
	}

	/**
	 * @return string[] - Ordererd method names to step N to.
	 */
	public static function steps(): array
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
			'Webserver',
		];
	}

	#############################
	### Config File Generator ###
	#############################
	/**
	 * turn a GDT field name into GDO_FIELD_NAME config constant name.
	 */
	public static function getConstantName(string $name): string
	{
		return sprintf('GDO_%s', strtoupper($name));
	}

	/**
	 * Auto-configure GDOv7 constants.
	 *
	 * @see self::fields()
	 */
	public static function configure(): void
	{
		$pro = Application::isPro();

		# Site
		deff('GDO_SITENAME', 'GDOv7');
		deff('GDO_HOSTNAME', gethostname());
		deff('GDO_ENV', 'dev');
		deff('GDO_SITECREATED', Time::getDate());
		deff('GDO_LANGUAGE', 'en');
		deff('GDO_TIMEZONE', ini_get('date.timezone')); # @TODO use the full timezone name here for the default timezone in module date.
		deff('GDO_THEMES', 'default');
		deff('GDO_MODULE', 'Core');
		deff('GDO_METHOD', 'Welcome');
		deff('GDO_SEO_URLS', false);
		deff('GDO_IPC', 'none');
		deff('GDO_IPC_DEBUG', false);
		deff('GDO_GDT_DEBUG', 0);
		deff('GDO_JSON_DEBUG', false);
		# HTTP
		deff('GDO_DOMAIN', $_SERVER['HTTP_HOST'] ?? 'localhost');
		deff('GDO_SERVER', self::detectServerSoftware());
		deff('GDO_PROTOCOL', @$_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
		deff('GDO_PORT', $_SERVER['SERVER_PORT'] ?: (GDO_PROTOCOL === 'https' ? 443 : 80));
		deff('GDO_WEB_ROOT', Strings::substrTo($_SERVER['SCRIPT_NAME'], 'install/wizard.php'));
		deff('GDO_FORCE_SSL', false);
		# Files
		deff('GDO_FILES_DIR', 'files');
		deff('GDO_CHMOD', 0770);
		deff('GDO_PREPROCESSOR', $pro);
		# Logging
		deff('GDO_LOG_REQUEST', $pro);
		deff('GDO_LOG_PROFILE', '');
		deff('GDO_ERROR_LEVEL', Logger::ALL);
		deff('GDO_ERROR_STACKTRACE', true);
		deff('GDO_ERROR_DIE', true);
		deff('GDO_ERROR_MAIL', false);
		# Database
		deff('GDO_SALT', Random::randomKey(16));
		deff('GDO_DB_ENABLED', true);
		deff('GDO_DB_READONLY', false);
		deff('GDO_DB_HOST', 'localhost');
		deff('GDO_DB_PORT', 3306);
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
		deff('GDO_SESS_NAME', 'GDO');
		deff('GDO_SESS_DOMAIN', GDO_DOMAIN);
		deff('GDO_SESS_TIME', Time::ONE_DAY * 7);
		deff('GDO_SESS_JS', false);
		deff('GDO_SESS_HTTPS', Application::$INSTANCE->isTLS());
		deff('GDO_SESS_SAMESITE', 'lax');
		deff('GDO_SESS_LOCK', GDO_DB_ENABLED);
		# Email
		deff('GDO_ENABLE_EMAIL', false);
		deff('GDO_BOT_NAME', GDO_SITENAME . ' Support System');
		deff('GDO_BOT_EMAIL', 'support@' . GDO_DOMAIN);
		deff('GDO_ADMIN_EMAIL', 'administrator@' . GDO_DOMAIN);
		deff('GDO_ERROR_EMAIL', 'errors@' . GDO_DOMAIN);
		deff('GDO_DEBUG_EMAIL', true);
	}

	private static function detectServerSoftware(): string
	{
		if (!isset($_SERVER['SERVER_SOFTWARE']))
		{
			return 'none';
		}
		$software = $_SERVER['SERVER_SOFTWARE'];
		if (stripos($software, 'Apache') !== false)
		{
			if (str_contains($software, '2.4'))
			{
				return 'apache2.4';
			}
			if (str_contains($software, '2.2'))
			{
				return 'apache2.2';
			}
			return 'apache2.4';
		}
		if (stripos($software, 'ngin') !== false)
		{
			return 'nginx';
		}
		return 'none';
	}

	/**
	 * GDT parameters for a config file.
	 */
	public static function fields(): array
	{
		static $fields = [];
		if (count($fields))
		{
			return $fields;
		}
		$fields = [
			GDT_Hidden::make('configured')->var('1'),

			# Site
			GDT_Divider::make()->label('install_config_section_site'),
			GDT_String::make('sitename')->initialValue(GDO_SITENAME)->max(16)->label('cfg_sitename')->tooltipRaw('should be configured via sitename language key.'),
			GDT_String::make('hostname')->notNull()->initialValue(GDO_HOSTNAME)->tooltipRaw('Server hostname. Used in error emails so you can distingush different servers.'),
			GDT_EnumNoI18n::make('env')->initial('dev')->enumValues('dev', 'tes', 'pro')->tooltipRaw('Environment can be dev, tes or pro.'),
			GDT_Checkbox::make('seo_urls')->initialValue(!!GDO_SEO_URLS)->tooltipRaw('Enable SEO style URLs. Requires url rewriting for your httpd.'),
			GDT_Hidden::make('sitecreated')->var(GDO_SITECREATED)->tooltipRaw('Automatically generated on config generation.'),
			GDT_EnumNoI18n::make('language')->enumValues(...GDO_Language::gdoSupportedISOs())->initial(GDO_LANGUAGE)->notNull()->tooltipRaw('Default Language setting. Should be \'en\''),
			GDT_String::make('timezone')->initialValue(GDO_TIMEZONE)->notNull()->tooltipRaw('Server Timezone for logfiles.'),
			GDT_String::make('themes')->notNull()->initial(GDO_THEMES)->tooltipRaw('Comma separated themechain list. Tried from left to right. Example: \'tbs,classic,default\'.'),
			GDT_String::make('module')->notNull()->initialValue(GDO_MODULE)->tooltipRaw('Default module for startpage.'),
			GDT_String::make('method')->notNull()->initialValue(GDO_METHOD)->tooltipRaw('Default method for startpage.'),
			GDT_Select::make('ipc')->choices(['db' => 'Database', 'ipc' => 'IPC', 'none' => 'none'])->initial(GDO_IPC)->tooltipRaw('IPC mode can be: db, ipc or none.'),
			GDT_Checkbox::make('ipc_debug')->initialValue(!!GDO_IPC_DEBUG)->tooltipRaw('IPC event logging.'),
			GDT_Int::make('gdt_debug')->unsigned()->initialValue((int)GDO_GDT_DEBUG)->min(0)->max(2)->tooltipRaw('GDT debugging level. 0: off, 1: counters, 2: instancelog.'),
			GDT_Checkbox::make('json_debug')->initialValue(!!GDO_JSON_DEBUG)->tooltipRaw('global JSON_PRETTY toggle.'),

			# HTTP
			GDT_Divider::make()->label('install_config_section_http'),
			GDT_String::make('domain')->notNull()->initialValue(GDO_DOMAIN)->tooltipRaw('Website domain. Should match cookie domain.'),
			GDT_Enum::make('server')->notNull()->enumValues('none', 'apache2.2', 'apache2.4', 'nginx', 'other')->initialValue(GDO_SERVER)->tooltipRaw('webserver software; apache2.2, apache2.4, nginx, none.'),
			GDT_String::make('domain')->notNull()->initialValue(GDO_DOMAIN)->tooltipRaw('Website domain. Should match cookie domain.'),
			GDT_String::make('web_root')->notNull()->initialValue(GDO_WEB_ROOT)->tooltipRaw('Website root folder. Usually "/" or "/phpgdo/".')->pattern('/^(\\/[^\\/]*)+$/'),
			GDT_Port::make('port')->notNull()->initialValue(GDO_PORT)->tooltipRaw('Default port for generating links.'),
			GDT_Enum::make('protocol')->notNull()->enumValues('http', 'https')->initialValue(GDO_PROTOCOL)->tooltipRaw('Website preferred protocol. Either http or https.'),
			GDT_Checkbox::make('force_ssl')->initial('0')->tooltipRaw('Allow only HTTPS?'),

			# Files
			GDT_Divider::make()->label('install_config_section_files'),
			GDT_Path::make('files_dir')->label('files_dir')->initial(GDO_FILES_DIR)->tooltipRaw('Filepath for physical files. Change this in config_test.php'),
			GDT_Enum::make('chmod')->enumValues('0700', '0770', '0777')->initial('0' . (base_convert((string)GDO_CHMOD, 10, 8)))->tooltipRaw('File creation chmod value. Ignore on windows.'),
			GDT_Checkbox::make('preprocessor')->initial('0')->tooltipRaw('File preprocessor to speed up dev code.'),

			# Logging
			GDT_Divider::make()->label('install_config_section_logging'),
			GDT_Checkbox::make('log_request')->initialValue(!!GDO_LOG_REQUEST)->tooltipRaw('Log every request?'),
			GDT_String::make('log_profile')->initialValue(GDO_LOG_PROFILE)->tooltipRaw('Generate URLs with xdebug profiler trigger?'),
			GDT_Hidden::make('error_level')->initialValue((int)GDO_ERROR_LEVEL)->tooltipRaw('Log level'),
			GDT_Checkbox::make('error_stacktrace')->initialValue(!!GDO_ERROR_STACKTRACE)->tooltipRaw('Show stacktrace to users?'),
			GDT_Checkbox::make('error_die')->initialValue(!!GDO_ERROR_DIE)->tooltipRaw('Die on every little warning and notice?'),
			GDT_Checkbox::make('error_mail')->initialValue(!!GDO_ERROR_MAIL)->tooltipRaw('Send an email on errors?'),

			# Database
			GDT_Divider::make()->label('install_config_section_database'),
			GDT_Hidden::make('salt')->initialValue(GDO_SALT)->tooltipRaw('Cryptograpycally secure salt to strengthen tokens and passwords.'),
			GDT_Checkbox::make('db_enabled')->initialValue(!!GDO_DB_ENABLED)->tooltipRaw('DB enabled? (required atm)'),
			GDT_Checkbox::make('db_readonly')->initialValue(!!GDO_DB_READONLY)->tooltipRaw('DB in read only mode? (except installers)'),
			GDT_String::make('db_host')->initialValue(GDO_DB_HOST)->tooltipRaw('DB hostname.'),
			GDT_Port::make('db_port')->initialValue((int)GDO_DB_PORT)->tooltipRaw('DB port.'),
			GDT_String::make('db_user')->initialValue(GDO_DB_USER)->tooltipRaw('DB username'),
			GDT_String::make('db_pass')->initialValue(GDO_DB_PASS)->tooltipRaw('DB password'),
			GDT_String::make('db_name')->initialValue(GDO_DB_NAME)->tooltipRaw('DB database name or SQLite filename'),
			GDT_EnumNoI18n::make('db_engine')->initial(GDO_DB_ENGINE)->enumValues(GDO::INNODB, GDO::MYISAM, GDO::SQL3_PERSIST, GDO::SQL3_WAL)->tooltipRaw('DB engine: InnoDB,MyIsam(MySQL), JournalMode(SQLite).'),
			GDT_Int::make('db_debug')->unsigned()->initialValue((int)GDO_DB_DEBUG)->min(0)->max(2)->tooltipRaw('GDO debugging level. 0: off, 1: counters, 2: instancelog.'),


			# Cache
			GDT_Divider::make()->label('install_config_section_cache'),
			GDT_UInt::make('cache_debug')->initialValue((int)GDO_CACHE_DEBUG)->min(0)->max(2)->tooltipRaw('Cache debugging level. 0: off, 1: setters, 2: setter-with-backtraces.'),
			GDT_Checkbox::make('filecache')->initialValue(!!GDO_FILECACHE)->tooltipRaw('Enable phpgdo filecache?'),
			GDT_Int::make('memcache')->unsigned()->min(0)->max(2)->initialValue((int)GDO_MEMCACHE)->tooltipRaw('Enable memcached? 0: off, 1: on, 2: fallback via filecache.'),
			GDT_String::make('memcache_host')->initialValue(GDO_MEMCACHE_HOST)->notNull()->tooltipRaw('memcached host.'),
			GDT_Port::make('memcache_port')->initialValue((int)GDO_MEMCACHE_PORT)->notNull()->tooltipRaw('memcached port.'),
			GDT_UInt::make('memcache_ttl')->unsigned()->initialValue((int)GDO_MEMCACHE_TTL)->notNull()->tooltipRaw('memcached time to live.'),

			# Cookies
			GDT_Divider::make()->label('install_config_section_cookies'),
			GDT_String::make('sess_name')->ascii()->caseS()->initialValue(GDO_SESS_NAME)->notNull()->tooltipRaw('Cookie name'),
			GDT_Hidden::make('sess_domain')->initialValue(GDO_SESS_DOMAIN)->tooltipRaw('Cookie domain. Use .domain.com for all subdomains.'),
			GDT_UInt::make('sess_time')->initialValue((int)GDO_SESS_TIME)->notNull()->min(30)->tooltipRaw('Session lifetime in seconds'),
			GDT_Checkbox::make('sess_js')->initialValue(!!GDO_SESS_JS)->tooltipRaw('Session cookie only secure via JS?'),
			GDT_Checkbox::make('sess_https')->initialValue(!!GDO_SESS_HTTPS)->tooltipRaw('Session only for https?'),
			GDT_Checkbox::make('sess_lock')->initialValue(!!GDO_SESS_LOCK)->tooltipRaw('Lock sessions during request?'),
			GDT_EnumNoI18n::make('sess_samesite')->enumValues('lax', 'none', 'strict')->initialValue(GDO_SESS_SAMESITE)->tooltipRaw('Session samesite settings. lax: recommended. none: wont work. strict: needs setup.'),

			# Email
			GDT_Divider::make()->label('install_config_section_email'),
			GDT_Checkbox::make('enable_email')->initialValue(!!GDO_ENABLE_EMAIL)->tooltipRaw('Enable E-Mail sending?'),
			GDT_String::make('bot_name')->notNull()->initialValue(GDO_BOT_NAME)->label('bot_name')->tooltipRaw('Robot Mail sender Name'),
			GDT_String::make('bot_email')->notNull()->initialValue(GDO_BOT_EMAIL)->label('bot_mail')->tooltipRaw('Robot Mail sender Mail'),
			GDT_String::make('admin_email')->notNull()->initialValue(GDO_ADMIN_EMAIL)->label('admin_mail')->tooltipRaw('Administrator Mail'),
			GDT_String::make('error_email')->notNull()->initialValue(GDO_ERROR_EMAIL)->label('error_mail')->tooltipRaw('Error Mail recipients. separate by comma.'),
			GDT_Checkbox::make('debug_email')->initialValue(!!GDO_DEBUG_EMAIL)->tooltipRaw('Enable Print to Screen debugging?'),
		];

		return $fields;
	}

}
