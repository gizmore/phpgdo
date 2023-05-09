<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Mail\Mail;
use GDO\Session\GDO_Session;
use GDO\UI\Color;
use GDO\UI\GDT_Page;
use GDO\UI\TextStyle;
use GDO\User\GDO_User;
use GDO\Util\Strings;
use Throwable;

/**
 * Debug backtrace and error handler.
 * Can send email on PHP errors, even fatals, if Module_Mail is installed.
 * Has a method to get debug timings.
 * Has a counter for unhandled exceptions.
 * Breakpoints for xdebug and a nice "global" scope can be set with @see break
 *
 * In Unit Test, verbose has to be enabled to show stack traces for exceptions.
 * In Unit Test, you can can use @see surpress to be able to test exceptions.
 *
 * @version 7.0.4
 * @since 3.0.1
 *
 * @example Debug::enableErrorHandler(); fatal_ooops();
 *
 * @author gizmore
 * @see Mail
 * @see Module_Perf
 */
final class Debug
{

	/**
	 * @var int - Surpress calculations
	 */
	private static int $ACTIVE = 1;


	/**
	 * @var int - PHP Errors not supressed
	 */
	public static int $ERRORS = 0; #PP#delete#

	/**
	 * @var int - Total exceptions not being handled by try/catch.
	 */
	public static int $EXCEPTIONS_UNHANDLED = 0; #PP#delete#

	/**
	 * @var int - Total execptions processed. Usually in catch() we process the exception normally, to not die.
	 */
	public static int $EXCEPTIONS_PROCESSED = 0; #PP#delete#

	final public const CLI_MAX_ARG_LEN = 100; # it's intersting that CLI can handle longer output

	final public const WWW_MAX_ARG_LEN = 50;

	public static int $MAX_ARG_LEN = self::WWW_MAX_ARG_LEN;

	private static bool $DIE = false;
	private static bool $ENABLED = false;
	private static bool $EXCEPTION = false;
	private static bool $MAIL_ON_ERROR = false;

	/**
	 * Call this to auto include.
	 */
	public static function init(bool $die = false, bool $mail = false): void
	{
		self::$MAX_ARG_LEN = php_sapi_name() === 'cli' ? self::CLI_MAX_ARG_LEN : self::WWW_MAX_ARG_LEN;
		self::setDieOnError($die);
		self::setMailOnError($mail);
		self::enableErrorHandler();
		self::enableExceptionHandler();
	}

	################
	### Surpress ###
	################


	/**
	 * Call something without Debug output and mail sending.
	 * @since 7.0.3
	 */
	public static function surpress(callable $callable, ...$args)
	{
		try
		{
			self::$ACTIVE = 0;
			return call_user_func_array($callable, $args);
		}
		finally
		{
			self::$ACTIVE = 1;
		}
	}


	################
	### Shutdown ###
	################

	public static function enableErrorHandler(): void
	{
		if (!self::$ENABLED)
		{
			set_error_handler([self::class, 'error_handler']);
			self::$ENABLED = true;
		}
	}

	#############
	### Break ###
	#############

	public static function enableExceptionHandler(): void
	{
		if (!self::$EXCEPTION)
		{
			self::$EXCEPTION = true;
			$handler = [self::class, 'exception_handler'];
			set_exception_handler($handler);
		}
	}

	###############
	## Settings ###
	###############

	public static function setDieOnError(bool $bool = true): void
	{
		self::$DIE = $bool;
	}

	public static function setMailOnError(bool $bool = true): void
	{
		self::$MAIL_ON_ERROR = $bool;
	}

	/**
	 * Trigger a breakpoint and gather global variables.
	 */
	public static function break(): bool
	{
		global $me;
		$app = Application::$INSTANCE;
		$page = GDT_Page::instance();
		$user = GDO_User::current();
		$modules = ModuleLoader::instance()->getModules();
		if (module_enabled('Session'))
		{
			$session = GDO_Session::instance();
		}
		xdebug_break();
		return $me && $app && $page && $user && $modules && isset($session);
	}

	#####################
	## Error Handlers ###
	#####################

	public static function disableErrorHandler(): void
	{
		if (self::$ENABLED)
		{
			restore_error_handler();
			self::$ENABLED = false;
		}
	}

	/**
	 * Error handler creates some html backtrace and can die on _every_ warning etc.
	 */
	public static function error_handler(int $errno, string $errstr, string $errfile, int $errline, mixed $errcontext = null): void
	{
		if ( (!(error_reporting() & $errno)) || (!self::$ACTIVE) )
		{
			return;
		}

		self::$ERRORS += self::$ACTIVE; #PP#delete#

		// Log as critical!
		if (class_exists('GDO\Core\Logger', false))
		{
			# But only if logger already in memory.
			$msg = sprintf('%s in %s line %s',
				$errstr, $errfile, $errline);
			Logger::logCritical(self::backtrace($msg, false));
			Logger::flush();
		}

		$errnostr = self::getPHPErrorName($errno);

		$app = Application::$INSTANCE;
		$is_html = !($app->isCLIOrUnitTest()) && $app->isHTML();

		$messageHTML = sprintf('<p>%s(EH %s):&nbsp;%s&nbsp;in&nbsp;<b style=\"font-size:16px;\">%s</b>&nbsp;line&nbsp;<b style=\"font-size:16px;\">%s</b></p>', $errnostr, $errno, $errstr, $errfile, $errline);
		$messageCLI = sprintf('%s(EH %s) %s in %s line %s.', Color::red($errnostr), $errno, TextStyle::boldi($errstr), TextStyle::bold($errfile), TextStyle::bold((string)$errline));
		$message = $is_html ? $messageHTML : $messageCLI;

		// Send error to admin
		self::sendDebugMail(self::backtrace($messageCLI, false));

		hdrc('HTTP/1.1 500 Server Error');

		$message = GDO_ERROR_STACKTRACE ? self::backtrace($message, $is_html) : $message;
		fwrite(STDERR, self::renderError($message));

		if (self::$DIE)
		{
			die(Application::EXIT_FATAL);
		}
	}

	/**
	 * Return a backtrace in either HTML or plaintext.
	 * You should use monospace font for html style rendering / pre tags.
	 * HTML means (x)html(5) and <pre> style.
	 * Plaintext means nice for logfiles.
	 */
	public static function backtrace(string $message = '', bool $html = true): string
	{
		return self::backtraceMessage($message, $html, debug_backtrace());
	}

	private static function backtraceMessage(string $message, bool $html, array $stack, string $lastLine = '?', string $lastFile = '[unknown file]'): string
	{
		// Fix full path disclosure
		$message = self::shortpath($message);

		// Append PRE header.
		$back = $html ? "<div class=\"gdo-exception\">\n" : '';

		// Append general title message.
		if ($message !== '')
		{
			$back .= $html ? '<em>' . $message . '</em>' : $message;
		}

		$implode = [];
		$preline = $lastLine;
		$prefile = $lastFile;
		$longest = 0;

		foreach ($stack as $row)
		{
			# Skip debugger trace
			if ((!isset($row['class'])) || ($row['class'] !== self::class))
			{
				# Build current call
				$function = sprintf('%s%s(%s)',
					isset($row['class']) ? $row['class'] . $row['type'] : '',
					$row['function'],
					self::backtraceArgs($row['args'] ?? null));

				# Collect relevant stack frame
				$implode[] = [
					$function,
					$prefile,
					$preline,
				];

				# Calculations for align
				$len = mb_strlen($function);
				$longest = max([$len, $longest]);
			}

			# Use line in next frame.
			$preline = $row['line'] ?? '?';
			$prefile = $row['file'] ?? '[unknown file]';
		}

		$copy = [];
		$cli = Application::$INSTANCE->isCLIOrUnitTest();
		$ajax = Application::$INSTANCE->isAjax();
		foreach ($implode as $imp)
		{
			[$func, $file, $line] = $imp;
			if ((!$cli) && (!$ajax))
			{
				$len = mb_strlen($func);
				$func .= ' ' . str_repeat('.', $longest - $len);
			}
			$copy[] = sprintf(' - %s %s line %s.', html($func), self::shortpath($file, "\n"), $line);
		}

		$back .= $html ? '<div class="gdt-hr"></div><pre>' : "\n";
		$back .= sprintf('Backtrace starts in %s line %s.', self::shortpath($prefile), $preline) . "\n";
		$back .= implode("\n", array_reverse($copy));
		$back .= $html ? "</pre>\n" : '';
		return $back;
	}

	/**
	 * Strip full pathes so we don't have a full path disclosure.
	 */
	public static function shortpath(string $path, string $newline = ''): string
	{
		$path = str_replace('\\', '/', $path); #PP#windows#
		$path = str_replace(GDO_PATH, '', $path);
		return trim($path, ' /');
	}

	private static function backtraceArgs(array $args = null): string
	{
		$out = [];
		if ($args)
		{
			foreach ($args as $arg)
			{
				$out[] = self::backtraceArg($arg);
			}
		}
		return implode(', ', $out);
	}

	private static function backtraceArg(mixed $arg=null): string
	{
		if ($arg === null)
		{
			return 'NULL';
		}
		elseif ($arg === true)
		{
			return 'true';
		}
		elseif ($arg === false)
		{
			return 'false';
		}
		elseif (is_object($arg))
		{
			$class = get_class($arg);
			$back = Strings::rsubstrFrom($class, '\\', $class);
			if ($arg instanceof GDO)
			{
				if (defined('GDO_CORE_STABLE'))
				{
					if (!$arg->gdoAbstract())
					{
						$back .= '#' . $arg->getID()??'0';
					}
				}
			}
			return $back;
		}
		else
		{
			$arg = json($arg);
		}

		$arg = html($arg);
		$arg = str_replace('&quot;', '"', $arg); # It is safe to inject quotes. Turn back to get length calc right.
		$arg = str_replace('\\\\', '\\', $arg); # Double backslash was escaped always via json encode?
		$arg = str_replace('\\/', '/', $arg); # Double backslash was escaped always via json encode?
		if (mb_strlen($arg) > self::$MAX_ARG_LEN)
		{
			# @TODO: Debug parameter value output shows buggy parameter value for strings that are close to the limit. like {"foo":"bar", "bar":"fo..., "bar:foo"}. Only some basic math is needed.
			return mb_substr($arg, 0, self::$MAX_ARG_LEN) . '…' . mb_substr($arg, -14);
		}
		return $arg;
	}

	/**
	 * Send error report mail.
	 */
	public static function sendDebugMail(string $message): bool
	{
		if (module_enabled('Mail'))
		{
			if (self::$MAIL_ON_ERROR)
			{
				try
				{
					return Mail::sendDebugMail('PHP Error', $message);
				}
				catch (Throwable $ex)
				{
					echo $ex->getTraceAsString();
				}
			}
		}
		return false;
	}

	private static function renderError(string $message): string
	{
		$app = Application::$INSTANCE;
		if ($app->isWebserver())
		{
			return html($message);
		}
		if ($app->isJSON())
		{
			return json(['error' => $message]);
		}
		if ($app->isCLIOrUnitTest())
		{
			return "$message\n";
		}
		return $message;
	}

	public static function exception_handler($ex): void
	{
		self::$EXCEPTIONS_UNHANDLED += self::$ACTIVE; #PP#delete#
		if (self::$ACTIVE)
		{
			echo self::debugException($ex);
		}
	}

	public static function debugException(Throwable $ex, bool $render = true): string
	{
		self::$EXCEPTIONS_PROCESSED += 1; #PP#delete#
		$app = Application::$INSTANCE;
		$is_html = $app->isHTML() && (!$app->isUnitTests());
		$firstLine = sprintf('%s in %s Line %s',
			$ex->getMessage(), $ex->getFile(), $ex->getLine());

		$message = self::backtraceException($ex, $is_html, ' (XH)');

		// Send error to admin?
		self::sendDebugMail($firstLine . "\n\n" . $message);

		// Log
		Logger::logException($ex);
		Logger::flush();

		if ($app->isCLIOrUnitTest())
		{
			fwrite(STDERR, "$message\n");
			if (ob_get_level())
			{
				ob_flush();
			}
			return '';
		}

		hdrc('HTTP/1.1 500 Server Error');

		return $render ? self::renderError($message) : '';
	}

	public static function backtraceException(Throwable $ex, bool $html = true, string $message = ''): string
	{
		$message = sprintf('%s: ´%s´ in %s line %s',
			Color::red(get_class($ex)), TextStyle::italic($ex->getMessage()),
			TextStyle::bold(self::shortpath($ex->getFile())),
			TextStyle::bold((string)$ex->getLine()));
		return self::backtraceMessage($message, $html, $ex->getTrace(), (string)$ex->getLine(), $ex->getFile());
	}

	public static function disableExceptionHandler(): void
	{
		if (self::$EXCEPTION)
		{
			restore_exception_handler();
			self::$EXCEPTION = false;
		}
	}

	/**
	 * Get some additional information
	 */
	public static function getDebugText(string $message): string
	{
		$user = '~~GHOST~~';
		if (class_exists('GDO\\User\\GDO_User', false))
		{
			try
			{
				$user = GDO_User::current()->renderUserName();
			}
			catch (Throwable $ex)
			{
				echo $ex->getTraceAsString();
			}
		}

		if ($url = trim(urldecode($_SERVER['REQUEST_URI']), '/'))
		{
			$url = GDO_PROTOCOL . '://' . GDO_DOMAIN . '/' . $url;
			$url = "<a href=\"{$url}\">{$url}</a>";
		}

		$args = [
			GDO_HOSTNAME,
			$_SERVER['REQUEST_METHOD'] ?? 'NULL',
			isset($_SERVER['REQUEST_URI']) ? $url : self::getMoMe(),
			$_SERVER['HTTP_REFERER'] ?? 'NULL',
			$_SERVER['REMOTE_ADDR'] ?? 'NULL',
			$_SERVER['USER_AGENT'] ?? 'NULL',
			$user,
			$message,
			print_r($_REQUEST, true),
		];
		foreach ($args as $i => $arg)
		{
			if ($i !== 7)
			{
				$arg = html($arg);
			}
			$args[$i] = $arg;
		}
		$pattern = "Hostname: %s\n
RequestMethod: %s\n
RequestURI: %s\n
Referer: %s\n
IP: %s\n
UserAgent: %s\n
GDO_User: %s\n\n
Message: %s\n\n
REQUEST: %s\n\n";
		return vsprintf($pattern, $args);
	}

	private static function getMoMe(): string
	{
		$mo = isset($_REQUEST['_mo']) ? (string)$_REQUEST['_mo'] : '-none';
		$me = isset($_REQUEST['_me']) ? (string)$_REQUEST['_me'] : 'none-';
		return "{$mo}/{$me}";
	}

	private static function getPHPErrorName(int $errno): string
	{
		switch ($errno)
		{
			case -1:
				return 'GDO Error';
			case E_ERROR:
			case E_CORE_ERROR:
				return 'PHP Fatal Error';
			case E_WARNING:
			case E_USER_WARNING:
			case E_CORE_WARNING:
				return 'PHP Warning';
			case E_USER_NOTICE:
			case E_NOTICE:
				return 'PHP Notice';
			case E_USER_ERROR:
				return 'PHP Error';
			case E_STRICT:
				return 'PHP Strict Error';
			case E_COMPILE_WARNING:
			case E_COMPILE_ERROR:
				return 'PHP Compile Error';
			case E_PARSE:
				return 'PHP Parse Error';
			case E_DEPRECATED:
				return 'PHP Deprecation Error';
			default:
				return sprintf('PHP Unknown Error 0x%x', $errno);
		}
	}

}

deff('GDO_HOSTNAME', gethostname()); #PP#delete#
