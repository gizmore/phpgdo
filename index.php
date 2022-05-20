<?php
use GDO\DB\Database;
use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\Core\Application;
use GDO\Core\ModuleLoader;
use GDO\Language\Trans;
use GDO\Session\GDO_Session;
use GDO\User\GDO_User;
use GDO\DB\Cache;
############
### Init ###
############
define('GDO_PERF_START', microtime(true));
@include 'protected/config.php';
if (!defined('GDO_CONFIGURED'))
{
	require 'index_install.php';
	die(1);
}
require 'GDO7.php';
new Application();
Logger::init(null, GDO_ERROR_LEVEL);
Debug::init(GDO_ERROR_DIE, GDO_ERROR_EMAIL);
Database::init();
ModuleLoader::init(GDO_DB_ENABLED);
Trans::$ISO = GDO_LANGUAGE;
if (!module_enabled('Core'))
{
	require 'index_install.php';
	die(1);
}
$noSession = true;
if (@class_exists('\\GDO\\Session\\GDO_Session', true))
{
	unset($noSession);
	GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS, GDO_SESS_SAMESITE);
	GDO_Session::instance();
}
$user = GDO_User::current();
Application::instance()->initThemes();
Logger::init($user->getName(), GDO_ERROR_LEVEL);
if (GDO_LOG_REQUEST)
{
	Logger::logRequest();
}
Application::instance()->handleJSONRequests();
define('GDO_CORE_STABLE', true);
############
### Main ###
############
$content = '';

##############
### Finish ###
##############
Cache::recacheHooks();
Application::timingHeader();
echo $content;
