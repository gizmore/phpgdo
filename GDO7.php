<?php
use GDO\Language\Trans;
use GDO\Core\GDO;
use GDO\Net\GDT_Url;
use GDO\User\GDO_User;
use GDO\Core\ModuleLoader;
use GDO\Core\Application;
use GDO\Date\Time;
use GDO\Util\Regex;
use GDO\Core\GDO_Module;
use GDO\Core\GDO_Error;
use GDO\Core\Method;
/**
 * GDO Autoloader and global functions.
 *
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
# Verbose error handling is default
define('GDO_PATH', str_replace('\\', '/', __DIR__) . '/');
error_reporting(E_ALL);
ini_set('display_errors', 1);

########################
### GDOv7 Autoloader ###
########################
global $GDT_LOADED;
$GDT_LOADED = 0; # perf
spl_autoload_register(function(string $name) : void
{
	if (str_starts_with($name, 'GDO\\')) # 1 line if
	{ # 2 lines path
		$name = str_replace('\\', '/', $name) . '.php';
		require GDO_PATH . $name;
		global $GDT_LOADED; # 2 lines perf
		$GDT_LOADED++;
	}
});

######################
### Global utility ###
######################
require GDO_PATH . 'GDO/Util/Shim.php';
new ModuleLoader(GDO_PATH . 'GDO/');

function sitename() : string
{
	return t('sitename');
}

function url(string $module, string $method, string $append = '', bool $lang = true) : string
{
	return GDT_Url::absolute(href($module, $method, $append, $lang));
}

function jxhref(string $module, string $method, string $append = '', bool $lang = true) : string
{
	return href($module, $method, $append . '&_ajax=1&_fmt=json', $lang);
}

function hrefDefault() : string
{
	return href(GDO_MODULE, GDO_METHOD);
}

function href(string $module, string $method, string $append = null, bool $lang = true) : string
{
	if (GDO_SEO_URLS)
	{
		$module = strtolower($module);
		$method = strtolower($method);
		$href = GDO_WEB_ROOT . "{$module}/{$method}";

		if ($append)
		{
			$append = ltrim($append, '&');
			$hashparts = explode('#', $append);
			$query = $hashparts[0];
			$hash = isset($hashparts[1]) ? $hashparts[1] : '';
			$qparts = explode('&', $query);
			$q = [];
			foreach ($qparts as $part)
			{
				if (( !strpos($part, '[')) && ( !str_starts_with($part, '_')))
				{
					$kv = explode('=', $part);
					$k = $kv[0];
					$v = seo($kv[1]);
					$href .= "/{$k}/{$v}";
				}
				else
				{
					$q[] = $part;
				}
			}
			if ($q)
			{
				$href .= '?' . implode('&', $q);
				if ($lang)
				{
					$href .= '&_lang=' . Trans::$ISO;
				}
			}
			elseif ($lang)
			{
				$href .= '?_lang=' . Trans::$ISO;
			}
			else
			{
				$href .= '?x=1';
			}
			if ($hash)
			{
				$href .= "#{$hash}";
			}
		}
	}
	else
	{
		$href = GDO_WEB_ROOT . "index.php?_mo={$module}&_me={$method}";
		if ($lang)
		{
			$href .= '&_lang=' . Trans::$ISO;
		}
		$href .= $append;
	}
	return $href;
}

function seo($str)
{
	return trim(preg_replace('#[^{}\\.\\p{L}0-9]#', '_', $str), '_');
}

function quote($value)
{
	return GDO::quoteS($value);
}

function json_quote($s)
{
	return str_replace("'", "&#39;", $s);
}

/**
 * HTML escaping.
 * @see \htmlspecialchars()
 */
function html(string $html=null) : string
{
	if ($html === null)
	{
		return '';
	}
	$app = Application::$INSTANCE;
	$is_html = $app->isHTML();
	$is_html = ($app->isCLI() || $app->isUnitTests()) ? false : $is_html;
	return $is_html ? str_replace(
	[
// 		'&',
		'"',
// 		"'",
		'<',
// 		'>'
	], [
// 		'&amp;',
		'&quot;',
// 		'&#39;',
		'&lt;',
		'&gt;'
	], $html) : $html;
}

function def(string $key, $default = null)
{
	return defined($key) ? constant($key) : $default;
}

function hdrc(string $header, bool $replace = true)
{
	hdr($header, $replace);
	$code = (int) Regex::firstMatch('#HTTP/1.1 (\\d+)#', $header);
	Application::setResponseCode($code);
}

function hdr(string $header, bool $replace = true)
{
	$app = Application::$INSTANCE;
	if ($app->isUnitTests())
	{
// 		echo $header . PHP_EOL;
	}
	elseif (!$app->isCLI())
	{
		header($header, $replace);
	}
}

function uridecode(string $url=null) : string
{
	return $url ? urldecode($url) : '';
}

/**
 * Check if a module is enabled.
 *
 * @param string $moduleName
 * @return boolean
 */
function module_enabled(string $moduleName) : bool
{
	if (ModuleLoader::instance()->getModule($moduleName, false, false))
	{
		return true;
	}
	return false;
}

function module(string $moduleName, bool $enabled=true, bool $fileSystem=false, bool $throw=true) : ?GDO_Module
{
	if ($module = ModuleLoader::instance()->getModule($moduleName, $fileSystem, $throw))
	{
		if ($enabled)
		{
			return $module->isEnabled() ? $module : null;
		}
		return $module;
	}
	if ($throw)
	{
		throw new GDO_Error('err_module', [html($moduleName)]);
	}
	
	return null;
}

function method(string $moduleName, string $methodName) : Method
{
	$func = ["GDO\\{$moduleName}\\Method\\$methodName", 'make'];
	return call_user_func($func);
}

# ######################
# ## Translation API ###
# ######################

/**
 * Global translate function to translate into current language ISO.
 * @return string|string[string]
 */
function t(string $key, array $args = null)
{
	return Trans::t($key, $args);
}

/**
 * Global translate function to translate into english.
 * @return string|string[string]
 */
function ten(string $key, array $args = null)
{
	return Trans::tiso('en', $key, $args);
}

/**
 * Global translate function to translate into an ISO language code.
 * @return string|string[string]
 */
function tiso(string $iso, string $key, array $args = null)
{
	return Trans::tiso($iso, $key, $args);
}

/**
 * Global translate function to translate into a user's language.
 * @return string|string[string]
 */
function tusr(GDO_User $user, string $key, array $args = null)
{
	return Trans::tiso($user->getLangISO(), $key, $args);
}

/**
 * Display a date value into current language iso.
 *
 * @param string $date
 *        Date in DB format.
 * @param string $format
 *        format key from trans file; e.g: 'short', 'long', 'date', 'exact'.
 * @param string $default
 *        the default string to display when date is null or invalid.
 * @return string
 */
function tt(string $date = null, string $format = 'short', string $default = '---') : string
{
	return Time::displayDate($date, $format, $default);
}
