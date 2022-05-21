<?php
use GDO\DB\Database;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\Core\Application;
use GDO\Core\ModuleLoader;
use GDO\Language\Trans;
use GDO\Session\GDO_Session;
use GDO\User\GDO_User;
use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\Core\GDO_Exception;
use GDO\Language\Module_Language;
use GDO\Core\Method;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Method;
use GDO\UI\GDT_HTML;
use GDO\UI\GDT_Page;
use GDO\Core\Method\DirectoryIndex;
use GDO\Core\Method\FileNotFound;
use GDO\Core\Method\Fileserver;
use GDO\Core\Method\SeoProxy;
use GDO\Core\Method\NotAllowed;
use GDO\Core\GDO_Error;
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
$app = Application::make();
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
	$session = GDO_Session::instance();
}
$user = GDO_User::current();
$app->initThemes();
Logger::init($user->getName(), GDO_ERROR_LEVEL);
if (GDO_LOG_REQUEST)
{
	Logger::logRequest();
}
$app->handleJSONRequests();
define('GDO_CORE_STABLE', true);
############
### Main ###
############
$_GET = $_POST = null;
$rqmethod = (string)@$_SERVER['REQUEST_METHOD'];
$isOptions = $rqmethod === 'OPTIONS';
if (!in_array($rqmethod, ['GET', 'POST', 'HEAD', 'OPTIONS'], true))
{
	$me = NotAllowed::make();
}
if (isset($_REQUEST['_lang']))
{
	Trans::$ISO = @$_REQUEST['_lang'];
	unset($_REQUEST['_lang']);
}
else
{
	Trans::$ISO = Module_Language::instance()->detectISO();
}

$mode = GDT::RENDER_HTML;
if (isset($_REQUEST['_fmt']))
{
	$mode = Application::instance()->detectRenderMode((string)@$_REQUEST['_fmt']);
	unset($_REQUEST['_fmt']);
}
$app->mode($mode);

$ajax = false;
if (isset($_REQUEST['_ajax']))
{
	$ajax = (bool)@$_REQUEST['_ajax'];
	unset($_REQUEST['_ajax']);
}
$app->ajax($ajax);

###################
### Pick Method ###
###################
if (!isset($_REQUEST['url']))
{
	if (isset($_REQUEST['mo']))
	{
		$mo = ModuleLoader::instance()->getModule((string)@$_REQUEST['mo']);
		$me = $mo->getMethod((string)@$_REQUEST['me']);
		if ($me instanceof Method)
		{
			unset($_REQUEST['mo']);
			unset($_REQUEST['me']);
			$me->inputs($_REQUEST);
			$result = GDT_Method::make()->method($me)->inputs($_REQUEST)->execute();
		}
		else
		{
			throw new GDO_Error('err_unknown_method', [html($mo->gdoHumanName()), html($_REQUEST['me'])]);
		}
	}
	else
	{
		$mo = ModuleLoader::instance()->getModule(GDO_MODULE);
		$me = $mo->getMethod(GDO_METHOD);
	}
}
else
{
	$url = (string) @$_REQUEST['url'];
	if (is_dir($url))
	{
		$me = DirectoryIndex::make();
	}
	elseif (is_file($url))
	{
		$me = Fileserver::make();
	}
	elseif (GDO_SEO_URLS)
	{
		$me = SeoProxy::make();
	}
	else
	{
		$me = FileNotFound::make();
	}
}
############
### Exec ###
############
$result = GDT_Method::make()->method($me)->inputs($_REQUEST)->execute();
$content = $result->renderMode($app->mode);
##############
### Finish ###
##############
Cache::recacheHooks();
Application::timingHeader();
echo $content;
