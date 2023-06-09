<?php
declare(strict_types=1);

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO_SEO_URL;
use GDO\Core\GDT;
use GDO\Core\GDT_Method;
use GDO\Core\GDT_Response;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\Method\DirectoryIndex;
use GDO\Core\Method\Error;
use GDO\Core\Method\FileNotFound;
use GDO\Core\Method\Fileserver;
use GDO\Core\Method\ForceSSL;
use GDO\Core\Method\NotAllowed;
use GDO\Core\Method\SeoProxy;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Language\Module_Language;
use GDO\Language\Trans;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_Error;
use GDO\UI\GDT_HTML;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;

/**
 * @var Method $me
 */
global $me; # one of the very few globals, required a lot in index.
/**
 * GDOv7 - The best PHP Framework in the solar system. Really!
 *
 * @version 7.0.3
 * @since 1.0.0
 * @author gizmore@wechall.net
 */
# Really, the first thing we do is measure performance :)
# Go Go Go GDOv7!
#gc_disable(); # GC slows things down? => Nope...
define('GDO_TIME_START', microtime(true));
#
#######################
### Bootstrap GDOv7 ###
#######################
require 'protected/config.php';
if (!defined('GDO_CONFIGURED'))
{
	require 'index_install.php'; # no config. bail out
}
require 'GDO7.php';
############
### Init ###
############
global $me;
$app = Application::init();
Logger::init(null, GDO_ERROR_LEVEL);
Debug::init(GDO_ERROR_DIE, GDO_ERROR_MAIL);
Database::init();
Trans::$ISO = GDO_LANGUAGE;
$loader = ModuleLoader::instance();
$loader->loadModulesCache(); # @TODO lazy module loading. This requires a complete change in how Hooks work.
if (!module_enabled('Core'))
{
	require 'index_install.php';
}
if ($app->hasSession())
{
	GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS, GDO_SESS_SAMESITE);
	$session = GDO_Session::instance();
}
$loader->initModules();    # @TODO lazy module initing. This requires a complete change of how Hooks are handled.
$user = GDO_User::current();
Logger::init($user->getName(), GDO_ERROR_LEVEL);
# First convert the response to readable.
$app->handleJSONRequests();
# Log it
if (GDO_LOG_REQUEST)
{
	Logger::logRequest();
}
###########
### ENV ###
###########
#
# HTTP Method. Deny anything not supported.
#
$rqmethod = (string)@$_SERVER['REQUEST_METHOD'];
if (!in_array($rqmethod, ['GET', 'POST', 'HEAD', 'OPTIONS'], true))
{
	$me = NotAllowed::make(); # early setting of method.
}
#
# Setup Language
#
if (isset($_REQUEST['_lang']))
{
	$iso = (string)$_REQUEST['_lang'];
	unset($_REQUEST['_lang']);
}
else
{
	$iso = Module_Language::instance()->detectISO();
}
Trans::setISO($iso);
#$loader->loadLangFiles();    # @TODO lazy module initing. This requires a complete change of how Hooks are handled.
define('GDO_CORE_STABLE', true); # all fine? @deprecated
#
# Remember GET/POST HTTP verb.
#
$app->verb(strtolower($_SERVER['REQUEST_METHOD']));
#
# Detect Content Type and set application render mode.
#
if (isset($_REQUEST['_fmt']))
{
	$mode = $app::detectRenderMode((string)@$_REQUEST['_fmt']);
	unset($_REQUEST['_fmt']);
}
else
{
	$mode = GDT::RENDER_WEBSITE;
}
$app->modeDetected($mode); # set detected mode.

###################
### Pick Method ###
###################
#
# index.php is called directly.
# Read $_GET _mo/_me
#
function gdoRouteMoMe(string $mo, string $me): Method
{
	if ($mo)
	{
		if (!($module = ModuleLoader::instance()->getModule($mo, true, false)))
		{
			$method = Error::make();
			$_REQUEST['error'] = t('err_unknown_module', [html($mo)]);
		}
		elseif ($me)
		{
			if (!($method = $module->getMethod($me)))
			{
				$method = Error::make();
				$_REQUEST['error'] = t('err_unknown_method', [
					$module->gdoHumanName(), html($me)]);
			}
		}
		else
		{
			$method = $module->getDefaultMethod();
		}
	}
	elseif ($module = ModuleLoader::instance()->getModule(GDO_MODULE, true, false))
		{
			if (!($method = $module->defaultMethod()))
			{
				$method = Error::make();
				$_REQUEST['error'] = t('err_unknown_method', [$module->gdoHumanName(), GDO_METHOD]);
			}
		}
		else
		{
			$method = Error::make();
			$_REQUEST['error'] = t('err_unknown_module', [GDO_MODULE]);
		}
	unset($_REQUEST['_mo']);
	unset($_REQUEST['_me']);
	unset($_REQUEST['_url']);
	return $method;
}

if (GDO_FORCE_SSL && (!Application::$INSTANCE->isTLS()))
{
	$me = ForceSSL::make();
}
elseif (!isset($_REQUEST['_url']) || empty($_REQUEST['_url']))
{
	$me = gdoRouteMoMe((string)@$_REQUEST['_mo'], (string)@$_REQUEST['_me']);
}
else
{
	# Wrap url
	$url = (string)@$_REQUEST['_url'];
	$url = $url ? "/{$url}" : '/index.html';
	$_REQUEST['url'] = $url;
	$url2 = ltrim($url, '/');

	# Cleanup
	unset($_REQUEST['_v']); # gdo version
	unset($_REQUEST['_av']); # asset version
	unset($_REQUEST['_url']); # seo url

	# Choose method for url
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
		if ($url2 = GDO_SEO_URL::getSEOUrl($url))
		{
			if (!($me = GDO_SEO_URL::getSEOMethod($url2)))
			{
				$_REQUEST['url'] = $url; # and a step back for 404 url :)
				$me = FileNotFound::make();
			}
		}
		else
		{
			$me = SeoProxy::makeProxied($url);
		}
	}
	else
	{
		$me = FileNotFound::make();
	}
}

# Remember ajax request option.
#
$ajax = false;
if (isset($_REQUEST['_ajax']))
{
	$ajax = (bool)@$_REQUEST['_ajax'];
	unset($_REQUEST['_ajax']);
}
if ($me->isAjax())
{
	$ajax = true;
}
$app->ajax($ajax);
############
### Exec ###
############
#$_GET = null; # from this point we have everything only in gdo.
#$_POST = null;
# plug together GDT_Method
$gdtMethod = GDT_Method::make()->method($me)->inputs($_REQUEST);
#
# Execute and force a GDO result.
#
try
{
	$result = $gdtMethod->execute();
	if (!($result instanceof GDT_Response))
	{
		$result = GDT_Response::make()->addFields($result);
	}
}
catch (Throwable $ex)
{
	# Send mail
	Debug::debugException($ex, false);
	# Error message result
	$result = GDT_Error::fromException($ex);
}

#
# If it is not a GDT_Response, wrap it.
# Because GDT_Response renders the GDT_Page template (in website, non ajax mode)
#
##############
### Finish ###
##############
# Commit session changes before net transfer...
if (isset($session) && $session)
{
	if (!Application::isCrash())
	{
		GDO_Session::commit(); # setting headers sometimes
	}
}
# Render the response.
$content = $result->render();
# The last thing we do before any output
$app->timingHeader(); # :) so every GDO request can be measured quickly.
##############.
### Output ###,
###############,
# Output asap. # Very late but still
echo $content; # asap
#########################
### fire IPC recaches ###
#########################
#Logger::flush(); # Done in Application
# @TODO On application exit, send mails while network is shuffleing

Cache::recacheHooks(); # we have time to recache now.
