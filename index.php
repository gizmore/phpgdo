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
use GDO\Language\Module_Language;
use GDO\Core\GDT_Method;
use GDO\Core\Method\DirectoryIndex;
use GDO\Core\Method\FileNotFound;
use GDO\Core\Method\Fileserver;
use GDO\Core\Method\SeoProxy;
use GDO\Core\Method\NotAllowed;
use GDO\Core\Method\Error;
use GDO\Core\GDT_Response;
use GDO\UI\GDT_Error;
# really the first thing we do :) Go Go GDOv7!
define('GDO_TIME_START', microtime(true)); 
#######################
### Bootstrap GDOv7 ###
#######################
@include 'protected/config.php';
if (!defined('GDO_CONFIGURED'))
{
	require 'index_install.php';
}
require 'GDO7.php';
############
### Init ###
############
$app = Application::instance();
Logger::init(null, GDO_ERROR_LEVEL);
Debug::init(GDO_ERROR_DIE, GDO_ERROR_EMAIL);
Database::init();
ModuleLoader::instance()->loadModulesCache();
Trans::$ISO = GDO_LANGUAGE;
if (!module_enabled('Core'))
{
	require 'index_install.php';
}
if (@class_exists('\\GDO\\Session\\GDO_Session', true))
{
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
define('GDO_CORE_STABLE', true); # all fine? @deprecated
###########
### ENV ###
###########
$_GET = $_POST = null; # cleanup unused stuff
# HTTP Method
$rqmethod = (string) @$_SERVER['REQUEST_METHOD'];
if (!in_array($rqmethod, ['GET', 'POST', 'HEAD', 'OPTIONS'], true))
{
	$me = NotAllowed::make();
}
# Language
if (isset($_REQUEST['_lang']))
{
	Trans::setISO((string) @$_REQUEST['_lang']);
	unset($_REQUEST['_lang']);
}
else
{
	Trans::$ISO = Module_Language::instance()->detectISO();
}

# Content Type
$mode = GDT::RENDER_HTML;
if (isset($_REQUEST['_fmt']))
{
	$mode = Application::$INSTANCE->detectRenderMode((string)@$_REQUEST['_fmt']);
	unset($_REQUEST['_fmt']);
}
$app->mode($mode, true); # set detected mode.

# Ajax
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
if (!isset($_REQUEST['_url']) || empty($_REQUEST['_url']))
{
	unset($_REQUEST['_url']);
	if (isset($_REQUEST['_mo']))
	{
		if (!($mo = ModuleLoader::instance()->getModule((string) @$_REQUEST['_mo'], true, false)))
		{
			$me = Error::make();
			$_REQUEST['error'] = t('err_unknown_module', [html((string)$_REQUEST['_mo'])]);
		}
		elseif (isset($_REQUEST['_me']))
		{
			if (!($me = $mo->getMethod((string) @$_REQUEST['_me'])))
			{
				$me = Error::make();
				$_REQUEST['error'] = t('err_unknown_method', [html($mo->gdoShortName()), html($_REQUEST['_me'])]);
			}
		}
		else
		{
			$me = $mo->getDefaultMethod();
		}
	}
	else
	{
		$mo = ModuleLoader::instance()->getModule(GDO_MODULE);
		$me = $mo->getMethod(GDO_METHOD);
	}
	unset($_REQUEST['_mo']);
	unset($_REQUEST['_me']);
}
else
{
	# Wrap url
	$url = (string) @$_REQUEST['_url'];
	$url = $url ? "/{$url}" : '/index.html';
	$_REQUEST['url'] = $url;
	$url2 = ltrim($url, '/');

	# Cleanup
	unset($_REQUEST['_av']);
	unset($_REQUEST['_url']);
	unset($_REQUEST['_v']);

	# Choose method
	if (is_dir($url2))
	{
		$me = DirectoryIndex::make();
	}
	elseif (is_file($url2))
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
Application::$INSTANCE->method($me);
$gdtMethod = GDT_Method::make()->method($me->withAppliedInputs($_REQUEST));
try
{
	$result = $gdtMethod->execute();
}
catch (\Throwable $t)
{
	$result =  GDT_Error::fromException($t);
}
if (!($result instanceof GDT_Response))
{
	$result = GDT_Response::make()->addField($result);
}
$content = $result->renderMode();
##############
### Finish ###
##############
Cache::recacheHooks();
if (isset($session))
{
	$session->commit();
}
Application::timingHeader(); # The last thing we do before any output
##############
### Output ###
##############
echo $content;
