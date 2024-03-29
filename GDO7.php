<?php
declare(strict_types=1);

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\Date\Time;
use GDO\Language\Trans;
use GDO\Net\GDT_Url;
use GDO\UI\TextStyle;
use GDO\User\GDO_User;
use GDO\Util\Regex;
use GDO\Util\Strings;

/**
 * GDO Autoloader and global functions.
 *
 * @version 7.0.4
 * @since 6.0.0
 * @author gizmore
 */
const GDO_PATH = __DIR__ . '/';

const GDO_SEO_SEP = ';';
#
##
########################
### GDOv7 Autoloader ###
########################
global $GDT_LOADED;
$GDT_LOADED = 0; #PP#delete#
#
/**
 * The infamous 5 line phpgdo autoloader... was cancelled.
 * I present now: the branchless autoloader! :)
 *
 * Generally both loaders work like this:
 * 1. Check if classname starts with `GDO\`
 * 2. Turn GDO\Module\Classname into a /fullpath.php (a tad faster on windows because a non required str_replace)
 * 3. Include the fullpath
 * 4. Increase performance counter, which is removed in production.
 *
 * @author gizmore
 */
spl_autoload_register(function (string $name): void
{
	# Branchless autoloader v7.02. slow
// 	$call = [
// 		function() { }, # ignore
// 		function($name){ # include
// 			$name = str_replace('\\', '/', $name); #PP#linux (only on linux) :)
// 			include(GDO_PATH . "{$name}.php"); # the worlds fastest autoloader.
// 			global $GDT_LOADED; # #PP#delete# performance metrics (only in dev)
// 			$GDT_LOADED++;      # #PP#delete#
// 		},
// 	];
// 	$call[((((ord($name[0]) << 8) | ord($name[3])) ^ 0xB8A3) + 1) >> 16]($name);

	# The original autoloader seems faster ;)
	if ($name[0] === 'G' && $name[3] === '\\') # 1 line for two if's
	{   # 2 lines for path + include
		$name = str_replace('\\', '/', $name); #PP#linux (i line only for linux systems)
		include(GDO_PATH . $name . '.php'); # load it!
		global $GDT_LOADED; # #PP#delete#
		$GDT_LOADED++;      # #PP#delete# - # Remove performance counter on production boxes via #PP#
	}
});

######################
### Global utility ###
######################
require GDO_PATH . 'GDO/Util/Shim.php';
new ModuleLoader(GDO_PATH . 'GDO/');

function sitename(): string
{
	return t('sitename');
}

function profile_link(string $username): string
{
    return \GDO\User\GDT_ProfileLink::make()->username($username)->render();
}

function url(string $module, string $method, string $append = '', bool $lang = true): string
{
    return GDT_Url::absolute(href($module, $method, $append, $lang));
}

function urlNoSeo(string $module, string $method, string $append = '', bool $lang = true): string
{
    return GDT_Url::absolute(hrefNoSeo($module, $method, $append));
}

function jxhref(string $module, string $method, string $append = '', bool $lang = true): string
{
	return href($module, $method, $append . '&_ajax=1&_fmt=json', $lang);
}

function hrefDefault(): string
{
    $module = ModuleLoader::instance()->getModule(GDO_MODULE);
    return $module->defaultMethod()->href();
}

/**
 * Create a GDOv7 href.
 * SEO: Turn an url like " Forum, Board, &id=3 " into " /forum/board/id/3 ".
 * Paramters with a dash or [] are not SEO converted.
 * Append timezone and language to an url via dash paramter.
 *
 * @see seo()
 */
function href(string $module, string $method, string $append = null, bool $seo = GDO_SEO_URLS): string
{
    $sep = GDO_SEO_SEP;
    $module = strtolower($module);
    $method = strtolower($method);
    if ($seo)
    {
        $href = GDO_WEB_ROOT . "{$module}{$sep}{$method}{$sep}";
        $q = [];
		$hash = '';
		$fmt = 'html';
		if ($append)
		{
			$append = ltrim($append, '&');
			$hashparts = explode('#', $append);
			$query = $hashparts[0];
			$hash = $hashparts[1] ?? GDT::EMPTY_STRING;
			$qparts = explode('&', $query);
			if ($qparts[0])
			{
				foreach ($qparts as $part)
				{
					if (str_starts_with($part, '_fmt'))
					{
						$fmt = Strings::substrFrom($part, '=');
					}
					elseif ((!strpos($part, '[')) && (!str_starts_with($part, '_')))
					{
						$kv = explode('=', $part);
						$k = $kv[0];
						$v = seo($kv[1]);
						$href .= "{$k}{$sep}{$v}{$sep}";
					}
					else
					{
						$q[] = $part;
					}
				}
            }
        }

        $href = rtrim($href, $sep);
        $href .= ".{$fmt}";

		if ($q)
		{
			$href .= '?' . implode('&', $q);
		}

		if (!isset($q['_lang']))
		{
			$href .= $q ? '&' : '?';
			$href .= '_lang=';
			$href .= Trans::$ISO;
		}

		#PP#start#
		if (GDO_LOG_PROFILE)
		{
			$href .= '&XDEBUG_TRIGGER=' . GDO_LOG_PROFILE;
		}
		#PP#end#
		if ($hash)
		{
			$href .= "#{$hash}";
		}
	}
	else
	{
		$href = GDO_WEB_ROOT . "index.php?_mo={$module}&_me={$method}";
//		if ($lang)
//		{
			$href .= '&_lang=' . Trans::$ISO;
//		}
		#PP#start#
		if (GDO_LOG_PROFILE)
		{
			$href .= '&XDEBUG_TRIGGER=' . GDO_LOG_PROFILE;
		}
		#PP#end#
		$href .= $append;
	}

	Application::$HREFS[] = $href; #PP#delete#

	return $href;
}

function hrefNoSeo(string $module, string $method, string $append = null): string
{
	return href($module, $method, $append, false);
}

function seo(string $str): string
{
	return trim(preg_replace('#[^~{}\\-_.,\\p{L}0-9]#', '_', $str), '_');
}

function quote($value): string
{
	return GDO::quoteS($value);
}

function json_quote(string $s): string
{
	return str_replace("'", '&#39;', $s);
}

/**
 * (almost) binary safe JSON.
 * Normal json_encode fails with false on unencodable glyphs.
 * In this case we use urlencode as a fallback :/
 * Use $pretty = 0 to disable global pretty print.
 */
function json(mixed $value, int $pretty = JSON_PRETTY_PRINT): string
{
	$enc = json_encode($value, GDO_JSON_DEBUG ? $pretty : 0);
	return $enc ?: uriencode(print_r($value, true));
}

/**
 * HTML escaping.
 * *Performance stunt*: Replace only the same character count to safe clock cycles. the func is probably a hot spot.
 * In CLI mode, shell paramter escaping and color code removal is done.
 *
 * @see htm
 * @see htmlspecialchars
 */
function html(?string $s): string
{
	$s = (string) $s;
	switch (Application::$MODE)
	{
		case GDT::RENDER_CLI:
			return CLI::removeColorCodes($s);

		case GDT::RENDER_BINARY:
			return $s;

		case GDT::RENDER_JSON:
			return json($s);

		default:
			return str_replace(
				[
					'&',
					'"',
					'<',
					'>',
				], [
				'&amp;',
				'&quot;',
				'&lt;',
				'&gt;',
			], $s);
	}
}

/**
 * A fast version of htmlspecialchars.
 * It can ONLY only be used inside double quotes!
 */
function htm(?string $html): string
{
	if ($html === null)
	{
		return GDT::EMPTY_STRING;
	}
	switch (Application::$MODE)
	{
		case GDT::RENDER_CLI:
		case GDT::RENDER_IRC:
		case GDT::RENDER_BINARY:
		case GDT::RENDER_JSON:
			return html($html);

		default:
			return str_replace(
				[
					'&',
					'"',
				], [
				'+',
				'\'',
			], $html);
	}
}

function def(string $key, mixed $default): mixed
{
	return defined($key) ? constant($key) : $default;
}

/**
 * Define a constant unless defined and return constants value.
 */
function deff(string $key, mixed $value): mixed
{
	if (!defined($key))
	{
		define($key, $value);
		return $value;
	}
	return constant($key);
}

function hdrc(string $header, bool $replace = true): void
{
	hdr($header, $replace);
	$code = (int)Regex::firstMatch('#HTTP/1.1 (\\d{3})#', $header);
	Application::setResponseCode($code);
}

function hdr(string $header, bool $replace = true): void
{
	$app = Application::$INSTANCE;
	if ($app->isUnitTests())
	{
		if ($app->isUnitTestVerbose())
		{
			printf("%s: %s\n", TextStyle::boldi('Header'), $header);
			if (ob_get_level())
			{
				ob_flush();
			}
		}
	}
	elseif ($app->isWebserver())
	{
		header($header, $replace);
	}
}

function uridecode(string $url = null): string
{
	return $url ? urldecode($url) : GDT::EMPTY_STRING;
}

function uriencode(string $url = null): string
{

	return $url ? str_replace('_', '-', urlencode($url)) : GDT::EMPTY_STRING;
}

/**
 * Check if a module is enabled.
 */
function module_enabled(string $moduleName): bool
{
	if ($module = ModuleLoader::instance()->getModule($moduleName, false))
	{
		return $module->isEnabled();
	}
	return false;
}

# ######################
# ## Translation API ###
# ######################

/**
 * Global translate function to translate into current language ISO.
 */
function t(string $key, array $args = null): string|array
{
	return Trans::t($key, $args);
}

/**
 * Global translate function to translate into english.
 */
function ten(string $key, array $args = null): string|array
{
	return Trans::tiso('en', $key, $args);
}

/**
 * Global translate function to translate into an ISO language code.
 */
function tiso(string $iso, string $key, array $args = null): string|array
{
	return Trans::tiso($iso, $key, $args);
}

/**
 * Global translate function to translate into a user's language.
 */
function tusr(GDO_User $user, string $key, array $args = null): string|array
{
	return Trans::tiso($user->getLangISO(), $key, $args);
}

/**
 * Display a date value into current language iso.
 */
function tt(string $date = null, string $format = 'short', string $default = '---'): string
{
	return Time::displayDate($date, $format, $default);
}
