<?php
declare(strict_types=1);
namespace GDO\CLI;

use GDO\Core\GDO_Error;
use GDO\Core\GDO_ErrorFatal;
use GDO\Core\Method;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Session\GDO_Session;
use GDO\UI\Color;
use GDO\UI\GDT_Container;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;

/**
 * CLI utility.
 *
 * @version 7.0.3
 * @since 6.10.2
 * @author gizmore
 * @see Method
 */
final class CLI
{

	public static function getLocale(): string
	{
		return getenv('LANG');
	}

	public static function isInteractive(): bool
	{
		return stream_isatty(STDIN);
	}

	/**
	 * @throws GDO_Error
	 * @throws GDO_ErrorFatal
	 */
	public static function setupUser(): GDO_User
	{
		$username = self::getUsername();
		if (!($user = GDO_User::getByName($username)))
		{
			$user = GDO_User::blank([
				'user_name' => $username,
				'user_type' => 'member',
			])->insert();
		}
		GDO_User::setCurrent($user);
		return $user;
	}

	/**
	 * Get the CLI username for the current user.
	 */
	public static function getUsername(): string
	{
		return get_current_user();
	}

	public static function getSingleCommandLine(): string
	{
		global $argv;
		array_shift($argv);
		return implode(' ', $argv);
	}

	public static function flushTopResponse(): void
	{
		echo self::getTopResponse();
		if (ob_get_level())
		{
			ob_flush();
		}
	}

	##############
	### Render ###
	##############

	public static function getTopResponse(): string
	{
		$response = GDT_Page::instance()->topResponse();
		# Render
		$result = $response->renderCLI();
		# Clear
		self::clearFlash($response);
		return $result;
	}

	private static function clearFlash(GDT_Container $response): void
	{
		$response->removeFields();
		if (module_enabled('Session'))
		{
			GDO_Session::remove('redirect_error');
			GDO_Session::remove('redirect_message');
		}
	}

	public static function displayCLI(string $html): string
	{
		return self::htmlToCLI($html);
	}

	/**
	 * Turn html into CLI output by stripping tags.
	 */
	public static function htmlToCLI(string $html): string
	{
		$html = preg_replace('#<a *href="([^"]+)">([^<]+)</a>#i', '$1 ($2)', $html);
		$html = self::br2nl($html);
		$html = preg_replace('#<[^>]*>#', '', $html);
		return html_entity_decode($html, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Turn <br/> into newlines.
	 */
	public static function br2nl(string $s, string $nl = "\n"): string
	{
		return preg_replace('#< *br */? *>#i', $nl, $s);
	}

	public static function red(string $s): string { return Color::red($s); }

	#############
	### Style ###
	#############

	public static function green(string $s): string { return Color::green($s); }

	public static function bold(string $s): string { return self::typemode($s, '1'); }

	private static function typemode(string $s, string $mode): string
	{
		return sprintf("\033[%sm%s\033[0m", $mode, $s);
	}

	public static function dim(string $s): string { return self::typemode($s, '2'); }

	public static function italic(string $s): string { return self::typemode($s, '3'); }

	public static function underlined(string $s): string { return self::typemode($s, '4'); }

	public static function blinking(string $s): string { return self::typemode($s, '5'); }

	public static function invisible(string $s): string { return self::typemode($s, '6'); }

	/**
	 * Simulate PHP $_SERVER vars.
	 */
	public static function setServerVars(): void
	{
		$_SERVER['HTTPS'] = 'off';
		$_SERVER['HTTP_HOST'] = GDO_DOMAIN;
		$_SERVER['SERVER_NAME'] = GDO_DOMAIN; # @TODO use machines host name.
		$_SERVER['SERVER_PORT'] = def('GDO_PORT', GDO_PROTOCOL === 'https' ? 443 : 80);
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1'; # @TODO use machines IP
		$_SERVER['HTTP_USER_AGENT'] = 'Firefox Gecko MS Opera';
		$_SERVER['REQUEST_URI'] = '/index.php?_mo=' . GDO_MODULE . '&_me=' . GDO_METHOD;
		$_SERVER['HTTP_REFERER'] = GDO_PROTOCOL . '://' . GDO_DOMAIN . '/referrer';
		$_SERVER['HTTP_ORIGIN'] = '127.0.0.1';
		$_SERVER['SCRIPT_NAME'] = GDO_WEB_ROOT . 'index.php';
		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4.41 (Win64) PHP/7.4.0';
		$_SERVER['HTTPS'] = 'off';
		$_SERVER['CONTENT_TYPE'] = 'application/gdo';
		$_SERVER['PHP_SELF'] = '/index.php';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['QUERY_STRING'] = '_mo=' . GDO_MODULE . '&_me=' . GDO_METHOD;
		$_SERVER['REQUEST_METHOD'] = GDT_Form::GET;
//		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = locale_get_default(); #'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7';
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7';
	}

	##############
	### Server ###
	##############

	/**
	 * Remove any terminal control characters.
	 */
	public static function removeColorCodes(string $s): string
	{
		return trim(preg_replace('/\x1B\\[[0-9;]\\{1,}[A-Za-z]/', '', $s));
	}

	#############
	### Usage ###
	#############

	/**
	 * Own implementation of escapeshellarg, because PHP limits it to 8kb.
	 */
	public static function escapeShell(string $s): string
	{
		return Process::isWindows() ? self::escapeShellWindows($s) : self::escapeShellLinux($s);
	}

	private static function escapeShellWindows(string $s): string
	{
		return '"' . addcslashes($s, '\\"') . '"';
	}

	private static function escapeShellLinux(string $s): string
	{
		return '\'' . str_replace('\'', '\\', $s) . '\'';
	}

	##############
	### Escape ###
	##############

//	private static function showHelp(Method $method)
//	{
//		return $method->renderCLIHelp();
//	}

	/**
	 * Render help line for gdt parameters.
	 */
	public static function renderCLIHelp(Method $method): string
	{
		$usage1 = [];
		$usage2 = [];

		# Ugly
		try
		{
			$method->onMethodInit();
		}
		catch (\Throwable)
		{
		}

		$fields = $method->gdoParameterCache();
		foreach ($fields as $gdt)
		{
			if ((!$gdt->isWriteable()) || ($gdt->isCLIHidden()))
			{
				continue;
			}
			if ($gdt->isPositional())
			{
				$label = $gdt->renderLabel();
				$xmplvars = $gdt->gdoExampleVars();
				$xmplvars = $xmplvars ?
					sprintf('<%s>(%s)', $label, $xmplvars) :
					sprintf('<%s>', $label);
				$usage1[] = $xmplvars;
			}
			elseif (!($gdt instanceof GDT_Submit))
			{
				$usage2[] = sprintf('[--%s=<%s>(%s)]',
					$gdt->getParameterAlias(), $gdt->gdoExampleVars(), $gdt->getVar());
			}
		}
		$usage = implode(',', $usage2) . ',' . implode(',', $usage1);
		$usage = trim($usage, ', ');
		$mome = $method->getCLITrigger();
		return ' ' . t('cli_usage', [
				trim(strtolower($mome) . ' ' . $usage), $method->getMethodDescription()]);
	}

//	private static function showMethods(GDO_Module $module): GDT_HTML
//	{
//		$methods = $module->getMethods();
//
//		$methods = array_filter($methods, function (Method $method)
//		{
//			return $method->isCLI();
//		});
//
//		$methods = array_map(function (Method $m)
//		{
//			return $m->gdoShortName();
//		}, $methods);
//
//		return GDT_HTML::make()->var(t('cli_methods', [
//			$module->renderName(), implode(', ', $methods)]));
//	}

	public static function isCLI(): bool
	{
		return php_sapi_name() === 'cli';
	}

	public static function init()
	{
		self::setServerVars();
	}

}

# Required gdo constants
#PP#start#
deff('GDO_DOMAIN', 'localhost');
deff('GDO_MODULE', 'Core');
deff('GDO_METHOD', 'Welcome');
#PP#end#
