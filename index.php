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
use GDO\UI\GDT_HTML;
#
# really the first thing we do is measure performance :)
# Go Go Go! GDO7
#
define('GDO_TIME_START', microtime(true));
#
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
Trans::$ISO = GDO_LANGUAGE;
$loader = ModuleLoader::instance();
$loader->loadModulesCache(); # @TODO lazy module loading. This requires a complete change in how Hooks work.
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
$loader->initModules();	# @TODO lazy module initing. This requires a complete change of how Hooks are handled.
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
#
# HTTP Method. Deny anything not supported.
#
$rqmethod = (string) @$_SERVER['REQUEST_METHOD'];
if (!in_array($rqmethod, ['GET', 'POST', 'HEAD', 'OPTIONS'], true))
{
	$me = NotAllowed::make(); # early setting of method.
}

#
# Setup Language
#
if (isset($_REQUEST['_lang']))
{
	Trans::setISO((string) @$_REQUEST['_lang']);
	unset($_REQUEST['_lang']);
}
else
{
	Trans::$ISO = Module_Language::instance()->detectISO();
}

#
# Remember GET/POST HTTP verb.
#
$app->verb($_SERVER['REQUEST_METHOD']);

#
# Detect Content Type and set application render mode.
# 
$mode = GDT::RENDER_HTML;
if (isset($_REQUEST['_fmt']))
{
	$mode = $app->detectRenderMode((string)@$_REQUEST['_fmt']);
	unset($_REQUEST['_fmt']);
}
$app->mode($mode, true); # set detected mode.

#
# Remember ajax request option
#
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
#
# We already have a method. This is a 403!
if (isset($me))
{
	# Patch all input to only the error!
	$_REQUEST = ['error' => (string) $_SERVER['REQUEST_METHOD']];
}
#
# index.php is called directly.
# Read $_GET _mo/_me
#
elseif (!isset($_REQUEST['_url']) || empty($_REQUEST['_url']))
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
		unset($_REQUEST['url']);
		$me = SeoProxy::makeProxied($url);
	}
	else
	{
		$me = FileNotFound::make();
	}
}

############
### Exec ###
############
$_GET = null; # from this point we have everything only in gdo.
$_POST = null;
$app->inputs($_REQUEST);
$app->method($me);
$gdtMethod = GDT_Method::make()->method($me)->inputs($_REQUEST);

#
# Execute and force a GDO result.
#
try
{
	if (null === ($result = $gdtMethod->execute()))
	{
		$result = GDT_HTML::make(); # empty response... okay? Oo?
	}

	elseif (is_string($result)) # text response, we wanna support that?
	{
		$result = GDT_HTML::withHTML($result);
	}
}
catch (\Throwable $t)
{
	# Error message result
	$result =  GDT_Error::fromException($t);
}

#
# If it is not a GDT_Response, wrap it.
# Because GDT_Response renders the GDT_Page template (in html, non ajax mode)
#
if (!($result instanceof GDT_Response))
{
	$result = GDT_Response::make()->addField($result);
}

# Render the response.
$content = $result->renderMode();

##############
### Finish ###
##############
if (isset($session))
{
	$session->commit(); # setting headers sometimes
}
# The last thing we do before any output
Application::timingHeader();
##############
### Output ###
##############
# Output asap. Very late but still
echo $content; # asap
#########################
### fire IPC recaches ###
#########################
Cache::recacheHooks();
