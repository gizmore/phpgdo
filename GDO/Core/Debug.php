<?php
namespace GDO\Core;

use GDO\Mail\Mail;
use GDO\User\GDO_User;
use GDO\Util\Strings;
use GDO\UI\Color;
use GDO\UI\TextStyle;
use GDO\UI\GDT_Page;
use GDO\Session\GDO_Session;

/**
 * Debug backtrace and error handler.
 * Can send email on PHP errors, even fatals, if Module_Mail is installed.
 * Has a method to get debug timings.
 * 
 * @example Debug::enableErrorHandler(); fatal_ooops();
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 3.0.1
 * @see Mail
 * @see Module_Perf
 */
final class Debug
{
	const CLI_MAX_ARG_LEN = 100; # it's intersting that CLI can handle longer output
	const WWW_MAX_ARG_LEN = 50;
	
	public static int $MAX_ARG_LEN = self::WWW_MAX_ARG_LEN;

	private static bool $DIE = false;
	private static bool $ENABLED = false;
	private static bool $EXCEPTION = false;
	private static bool $MAIL_ON_ERROR = false;
	
	/**
	 * Call this to auto include.
	 */
	public static function init(bool $die=false, bool $mail=false) : void
	{
		self::enableErrorHandler();
		self::enableExceptionHandler();
		self::setDieOnError($die);
		self::setMailOnError($mail);
	}
	
	#############
	### Break ###
	#############
	/**
	 * Trigger a breakpoint and gather global variables.
	 * @deprecated unused
	 */
	public static function break() : bool
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
	
	###############
	## Settings ###
	###############
	public static function setDieOnError(bool $bool = true) : void
	{
		self::$DIE = $bool;
	}
	
	public static function setMailOnError(bool $bool = true) : void
	{
		self::$MAIL_ON_ERROR = $bool;
	}
	
	public static function disableErrorHandler() : void
	{
		if (self::$ENABLED)
		{
			restore_error_handler();
			self::$ENABLED = false;
		}
	}
	
	public static function enableErrorHandler() : void
	{
		if (!self::$ENABLED)
		{
			set_error_handler([self::class, 'error_handler']);
// 			register_shutdown_function([self::class, 'shutdown_function']);
			self::$ENABLED = true;
		}
	}
	
	#####################
	## Error Handlers ###
	#####################
	/**
	 * @TODO: shutdown function shall show debug stacktrace on fatal error. If an error was already shown, print nothing.
	 * No stacktrace available and some vars are messed up.
	 */
	public static function shutdown_function()
	{
		if ($error = error_get_last())
		{
			$type = $error['type'];
			if ($type === 64)
			{
				self::error_handler($type, $error['message'], self::shortpath($error['file']), $error['line'], null);
			}
			die(-1);
		}
	}
	
	public static function error(\Throwable $ex)
	{
	    self::error_handler($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
	}
	
	/**
	 * Error handler creates some html backtrace and can die on _every_ warning etc.
	 * 
	 * @param int $errno			
	 * @param string $errstr			
	 * @param string $errfile			
	 * @param int $errline			
	 * @param mixed $errcontext
	 */
	public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext=null)
	{
	    if (!(error_reporting() & $errno))
		{
			return;
		}
		
		// Log as critical!
		if (class_exists('GDO\Core\Logger', false))
		{
			# But only if logger already in memory.
			$msg = sprintf('%s in %s line %s',
				$errstr, $errfile, $errline);
			Logger::logCritical(self::backtrace($msg, false));
			Logger::flush();
		}
		
		switch ($errno)
		{
			case -1:
				$errnostr = 'GDO Error';
				break;
			case E_ERROR:
			case E_CORE_ERROR:
				$errnostr = 'PHP Fatal Error';
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_CORE_WARNING:
				$errnostr = 'PHP Warning';
				break;
			case E_USER_NOTICE:
			case E_NOTICE:
				$errnostr = 'PHP Notice';
				break;
			case E_USER_ERROR:
				$errnostr = 'PHP Error';
				break;
			case E_STRICT:
				$errnostr = 'PHP Strict Error';
				break;
			case E_COMPILE_WARNING:
			case E_COMPILE_ERROR:
				$errnostr = 'PHP Compile Error';
				break;
			case E_PARSE:
				$errnostr = 'PHP Parse Error';
				break;
			case E_DEPRECATED:
				$errnostr = 'PHP Deprecation Error';
				break;
			default:
				$errnostr = 'PHP Unknown Error';
				break;
		}
		
		$app = Application::$INSTANCE;
		$is_html = $app->isHTML();
		$is_html = ($app->isCLI() || $app->isUnitTests()) ? false : $is_html;
		
		$messageHTML = sprintf('<p>%s(EH %s):&nbsp;%s&nbsp;in&nbsp;<b style=\"font-size:16px;\">%s</b>&nbsp;line&nbsp;<b style=\"font-size:16px;\">%s</b></p>', $errnostr, $errno, $errstr, $errfile, $errline);
		$messageCLI = sprintf('%s(EH %s) %s in %s line %s.', Color::red($errnostr), $errno, TextStyle::italic($errstr), TextStyle::bold($errfile), TextStyle::bold($errline));
		$message = $is_html ? $messageHTML : $messageCLI;
		
		// Send error to admin
		if (self::$MAIL_ON_ERROR)
		{
			self::sendDebugMail(self::backtrace($messageCLI, false));
		}
		
		hdrc('HTTP/1.1 500 Server Error');

		// Output error
		if ($app->isCLI() || $app->isUnitTests())
		{
			fwrite(STDERR, self::backtrace($messageCLI, false) . PHP_EOL);
		}
		else
		{
			$message = GDO_ERROR_STACKTRACE ? self::backtrace($message, $is_html) : $message;
			echo self::renderError($message);
		}
		
		return self::$DIE ? die(1) : true;
	}
	
	public static function exception_handler($ex) : void
	{
	    echo self::debugException($ex);
// 	    die(1);
	}
	
	public static function debugException(\Throwable $ex, bool $render=true) : string
	{
	    $app = Application::instance();
	    $is_html = $app->isHTML() && (!$app->isUnitTests());
	    $firstLine = sprintf("%s in %s Line %s",
	    	$ex->getMessage(), $ex->getFile(), $ex->getLine());
	    
	    $log = true;
	    $mail = self::$MAIL_ON_ERROR;
	    $message = self::backtraceException($ex, $is_html, ' (XH)');
	    
	    // Send error to admin?
	    if ($mail)
	    {
	        self::sendDebugMail($firstLine . "\n\n" . $message);
	    }
	    
	    // Log it?
	    if ($log)
	    {
	    	Logger::logException($ex);
	        Logger::flush();
	    }
	    
	    if ($app->isUnitTests())
	    {
	    	echo $message . "\n";
	    	@ob_flush();
	    }
	    
	    hdrc('HTTP/1.1 500 Server Error');
	    
	    if ($render)
	    {
	        return self::renderError($message);
	    }
	    
	    return '';
	}
	
	private static function renderError(string $message) : string
	{
		$app = Application::$INSTANCE;
		if (!$app)
		{
		    return html($message);
		}
		if ($app->isJSON())
		{
			return json_encode(['error' => $message]);
		}
		if ($app->isCLI() || $app->isUnitTests())
		{
		    return "$message\n";
		}
	    return $message;
	}
	
	public static function disableExceptionHandler() : void
	{
		if (self::$EXCEPTION)
		{
			restore_exception_handler();
			self::$EXCEPTION = false;
		}
	}
	
	public static function enableExceptionHandler() : void
	{
		if (!self::$EXCEPTION)
		{
			self::$EXCEPTION = true;
			$handler = [self::class, 'exception_handler'];
			set_exception_handler($handler);
		}
	}
	
	/**
	 * Send error report mail.
	 */
	public static function sendDebugMail(string $message) : bool
	{
		if (module_enabled('Mail'))
		{
			return Mail::sendDebugMail('PHP Error', $message);
		}
		return false;
	}
	
	/**
	 * Get some additional information
	 * 
	 * @TODO move?
	 */
	public static function getDebugText(string $message) : string
	{
		$user = "~~GHOST~~";
		if (class_exists('GDO\\User\\GDO_User', false))
		{
    		try { $user = GDO_User::current()->renderUserName(); } catch (\Throwable $ex) { }
		}
		
		if ($url = trim(@urldecode(@$_SERVER['REQUEST_URI']), '/'))
		{
		    $url = GDO_PROTOCOL . '://' . GDO_DOMAIN . '/' . $url;
		    $url = "<a href=\"{$url}\">{$url}</a>";
		}
		
		$args = [
			isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'NULL',
			isset($_SERVER['REQUEST_URI']) ? $url : self::getMoMe(),
			isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'NULL',
			isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'NULL',
			isset($_SERVER['USER_AGENT']) ? $_SERVER['USER_AGENT'] : 'NULL',
			$user,
			$message,
		    print_r($_REQUEST, true),
		];
		foreach ($args as $i => $arg)
		{
			if ($i !== 6)
			{
				$arg = html($arg);
			}
			$args[$i] = $arg;
		}
		$pattern = "RequestMethod: %s\n
RequestURI: %s\n
Referer: %s\n
IP: %s\n
UserAgent: %s\n
GDO_User: %s\n\n
Message: %s\n\n
REQUEST: %s\n\n";
		return vsprintf($pattern, $args);
	}
	
	private static function getMoMe() : string
	{
		$mo = isset($_REQUEST['_mo']) ? (string)$_REQUEST['_mo'] : '-none';
		$me = isset($_REQUEST['_me']) ? (string)$_REQUEST['_me'] : 'none-';
		return "{$mo}/{$me}";
	}
	
	/**
	 * Return a backtrace in either HTML or plaintext.
	 * You should use monospace font for html style rendering / pre tags.
	 * HTML means (x)html(5) and <pre> style.
	 * Plaintext means nice for logfiles.
	 */
	public static function backtrace(string $message = '', bool $html = true)
	{
		return self::backtraceMessage($message, $html, debug_backtrace());
	}
	
	public static function backtraceException(\Throwable $ex, bool $html = true, string $message = '') : string
	{
		$message = sprintf("%s: ´%s´ in %s line %s",
			Color::red(get_class($ex)), TextStyle::italic($ex->getMessage()),
			TextStyle::bold(self::shortpath($ex->getFile())),
			TextStyle::bold($ex->getLine()));
		return self::backtraceMessage($message, $html, $ex->getTrace(), $ex->getLine(), $ex->getFile());
	}
	
	private static function backtraceArgs(array $args = null) : string
	{
		$out = [];
		if ($args)
		{
			foreach ($args as $arg)
			{
				$out[] = self::backtraceArg($arg);
			}
		}
		return implode(", ", $out);
	}
	
	private static function backtraceArg($arg) : string
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
						$back .= '#' . $arg->getID();
					}
				}
			}
			return $back;
		}
		else
		{
			$arg = json_encode($arg);
		}
		
		$app = Application::$INSTANCE;
		$is_html = $app ? $app->isHTML() : (php_sapi_name() !== 'cli');
		$is_html = ($app->isCLI() || $app->isUnitTests()) ? false : $is_html;
// 		$is_html = $app ? $app->isHTML() && (!$app->isUnitTests()) : true;
		
		$arg = $is_html ? html($arg) : $arg;
		$arg = str_replace('&quot;', '"', $arg); # It is safe to inject quotes. Turn back to get length calc right.
		$arg = str_replace('\\\\', '\\', $arg); # Double backslash was escaped always via json encode?
		$arg = str_replace('\\/', '/', $arg); # Double backslash was escaped always via json encode?
		if (mb_strlen($arg) > self::$MAX_ARG_LEN)
		{
			if ($app->isCLI() || $app->isUnitTests())
			{
				self::$MAX_ARG_LEN = self::CLI_MAX_ARG_LEN;
			}
			else
			{
				self::$MAX_ARG_LEN = self::WWW_MAX_ARG_LEN;
			}
			# @TODO: Debug parameter value output shows buggy parameter value for strings that are close to the limit. like {"foo":"bar", "bar":"fo..., "bar:foo"}. Only some basic math is needed.
		    return mb_substr($arg, 0, self::$MAX_ARG_LEN) . '…' . mb_substr($arg, -14);
		}
		
		return $arg;
	}

	private static function backtraceMessage(string $message, bool $html, array $stack, string $lastLine='?', string $lastFile='[unknown file]') : string
	{
		// Fix full path disclosure
		$message = self::shortpath($message);
		
		if (!GDO_ERROR_STACKTRACE)
		{
			return $html ? sprintf('<div class="gdo-exception">%s</div>', $message) . PHP_EOL : $message;
		}
		
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
		    if (@$row['class'] !== self::class)
		    {
		        # Build current call
				$function = sprintf('%s%s(%s)',
				    isset($row['class']) ? $row['class'] . $row['type'] : '',
				    $row['function'],
				    self::backtraceArgs(isset($row['args']) ? $row['args'] : null));
				
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
			$preline = isset($row['line']) ? $row['line'] : '?';
			$prefile = isset($row['file']) ? $row['file'] : '[unknown file]';
		}
		
		$copy = [];
		$cli = Application::$INSTANCE->isCLI();
		$ajax = Application::$INSTANCE->isAjax();
		foreach ($implode as $imp)
		{
			list ($func, $file, $line) = $imp;
			if ( (!$cli) && (!$ajax) )
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
	 * 
	 * @param string $path			
	 * @return string
	 */
	public static function shortpath(string $path, string $newline="") : string
	{
		$path = str_replace('\\', '/', $path);
		$path = str_replace(GDO_PATH, '', $path);
		$path = trim($path, ' /');
		return $path;
	}
	
}
