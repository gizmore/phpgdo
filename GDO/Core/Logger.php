<?php
namespace GDO\Core;

use DateTime;
use DateTimeZone;
use Throwable;

/**
 * The GDO Logger.
 * Buffered.
 * One logfile per user.
 *
 * @version 7.0.2
 * @since 1.0.0
 * @author gizmore
 * @author spaceone
 */
final class Logger
{

	public const GDO_WARNING = 0x01;
	public const GDO_MESSAGE = 0x02;
	public const GDO_ERROR = 0x04;
	public const GDO_CRITICAL = 0x08;
	public const PHP_ERROR = 0x10;
	public const DB_ERROR = 0x20;
	public const HTTP_ERROR = 0x80;
	public const HTTP_GET = 0x100;
	public const HTTP_POST = 0x200;
	public const IP = 0x400;
	public const BUFFERED = 0x1000;
	public const DEBUG = 0x2000;

	public const _NONE = 0x00;
	public const _ALL = 0x37ff;
	public const _DEFAULT = self::_ALL;

	public static int $WRITES = 0;
	public static string $POST_DELIMITER = '.::.';
	public static ?DateTimeZone $TIMEZONE;

	private static ?string $username;
	private static string $basedir = GDO_PATH . 'protected/logs';
	private static int $logbits = self::_DEFAULT;
	private static string $logformat = "%s [%s%s] - %s\n";
	private static int $cache = 0; # cached logbits
	private static array $logs = [];

	/**
	 * Init the logger. If a username is given, the logger will log into a logs/username dir.
	 *
	 * @param string $username The username for memberlogs
	 * @param int $logbits bitmask for logging-modes
	 * @param string $basedir The path to the logfiles. Should be relative.
	 */
	public static function init(string $username = null, int $logbits = self::_DEFAULT, string $basedir = 'protected/logs'): void
	{
		self::$username = $username;
		self::$logbits = $logbits;
		self::$basedir = GDO_PATH . $basedir;
		self::$TIMEZONE = new DateTimeZone(GDO_TIMEZONE);
	}

	public static function cache(int $newLogbits)
	{
		self::$cache = self::$logbits;
		self::$logbits = $newLogbits;
	}

	public static function restore() { self::$logbits = self::$cache; }

	public static function setLogFormat($format) { self::$logformat = $format; }

	public static function enableBuffer() { self::enable(self::BUFFERED); }

	public static function enable($bits) { self::$logbits |= $bits; }

	public static function disableBuffer()
	{
		self::flush();
		self::disable(self::BUFFERED);
	}

	/**
	 * Flush all logfiles.
	 *
	 * @throws GDO_Exception
	 */
	public static function flush(): void
	{
		foreach (self::$logs as $file => $msg)
		{
			if ($e = self::writeLog($file, $msg))
			{
				unset(self::$logs[$file]);
			}
			else
			{
				throw new GDO_Exception('Cannot write log to ' . $file);
			}
		}
	}

	/**
	 * Log a message. maybe twice.
	 */
	private static function writeLog(string $filename, string $message): bool
	{
		if (self::$username)
		{
			if (!self::writeLogB(self::$username, $filename, $message))
			{
				return false;
			}
		}
		return self::writeLogB(null, $filename, $message);
	}

	private static function writeLogB(?string $username, string $filename, string $message): bool
	{
		# Create logdir if not exists
		$filename = self::getFullPath($filename, $username);
		if (!self::createLogDir($filename))
		{
			throw new GDO_Error('err_create_dir', [dirname($filename), __METHOD__, __LINE__]);
		}

		# Default kill banner.
		if (!is_file($filename))
		{
			$bool = true;
			$bool = $bool && (false !== file_put_contents($filename, '<?php die(2); ?>' . PHP_EOL));
			$bool = $bool && @chmod($filename, GDO_CHMOD & 0666);
			if (false === $bool)
			{
				throw new GDO_Exception(sprintf('Cannot create logfile "%s" in %s line %s.', $filename, __METHOD__, __LINE__));
			}
		}

		# Write to file
		if (!file_put_contents($filename, $message, FILE_APPEND))
		{
			throw new GDO_Exception(sprintf('Cannot write logs: logfile "%s" in %s line %s.', $filename, __METHOD__, __LINE__));
		}

		return true;
	}

	/**
	 * Get the full log path, either for username log or site log.
	 *
	 * @param string $filename
	 * @param string|false $username
	 */
	private static function getFullPath(string $filename, string $username = null)
	{
		$dt = new DateTime('now', self::$TIMEZONE);
		$date = $dt->format('Ymd');
		return is_string($username)
			? sprintf('%s/memberlog/%s/%s_%s.txt', self::$basedir, $username, $date, $filename)
			: sprintf('%s/%s_%s.txt', self::$basedir, $date, $filename);
	}

	/**
	 * Recursively create logdir with GDO_CHMOD permissions.
	 *
	 * @param string $filename
	 *
	 * @return bool
	 */
	private static function createLogDir($filename)
	{
		$dir = dirname($filename);
		return is_dir($dir) ? true : mkdir($dir, GDO_CHMOD, true);
	}

	public static function disable($bits) { self::$logbits &= (~$bits); }

	/**
	 * Log the request.
	 */
	public static function logRequest(): void
	{
		self::log('request', self::getRequest());
	}

	/**
	 * Log a message.
	 * The core logging function.
	 * Raw mode will not write any datestamps or IP/username.
	 * format: $time, $ip, $username, $message
	 */
	public static function log(string $filename, string $message, int $logmode = 0): void
	{
		# log it?
		if (self::isEnabled($logmode))
		{
			$dt = new DateTime('now', self::$TIMEZONE);
			$time = $dt->format('H:i');
			$ip = self::isDisabled(self::IP) ? '' : @$_SERVER['REMOTE_ADDR'];
			$username = self::$username === false ? ':~guest~' : ':' . self::$username;
			self::logB($filename, sprintf(self::$logformat, $time, $ip, $username, $message));
		}
	}

	########################
	### Default logfiles ###
	########################

	public static function isEnabled(int $bits): bool { return ($bits === (self::$logbits & $bits)); }

	public static function isDisabled(int $bits): bool { return ($bits !== (self::$logbits & $bits)); }

	private static function logB($filename, $message)
	{
		self::$WRITES++; #PP#delete#
		if (!self::isBuffered())
		{
			self::writeLog($filename, $message);
		}
		elseif (isset(self::$logs[$filename]))
		{
			self::$logs[$filename] .= $message;
		}
		else
		{
			self::$logs[$filename] = $message;
		}
	}

	public static function isBuffered() { return self::isEnabled(self::BUFFERED); }

	/**
	 * Get the whole request to log it. Censor passwords.
	 *
	 * @return string
	 */
	private static function getRequest()
	{
		$back = $_SERVER['REQUEST_METHOD'];
		$back .= ' ';
		$back .= urldecode($_SERVER['REQUEST_URI']);
		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$back .= self::$POST_DELIMITER . 'POSTDATA: ' . self::stripPassword($_REQUEST);
		}
		return $back;
	}

	/**
	 * strip values from arraykeys which begin with 'pass'
	 *
	 * @TODO faster way without foreach...
	 * print_r and preg_match ?
	 * array_map stripos('pass') return '';
	 */
	private static function stripPassword(array $a)
	{
		$back = '';
		foreach ($a as $k => $v)
		{
			if (stripos($k, 'pass') !== false)
			{
				$v = 'xxxxx';
			}
			elseif (is_array($v) === true)
			{
				$v = 'Array(' . count($v) . ')';
			}
			$back .= self::$POST_DELIMITER . $k . '=>' . $v;
		}
		return self::shortString($back);
	}

	/**
	 * shorten a string and remove dangerous pattern
	 *
	 */
	public static function &shortString(&$str, $length = 256)
	{
		$str = substr($str, 0, $length);
		$str = str_replace('<?', '##', $str);
		return $str;
	}

	public static function logCron($message): void
	{
		self::rawLog('cron', $message, 0);
		if (!Application::$INSTANCE->isUnitTests())
		{
			echo $message . PHP_EOL;
		}
	}

	public static function rawLog($filename, $message, $logmode = 0)
	{
		# log it?
		if (self::isEnabled($logmode))
		{
			self::logB($filename, $message . PHP_EOL);
		}
	}

	public static function logWebsocket($message)
	{
		self::rawLog('websocket', $message, 0);
		echo $message . PHP_EOL;
	}

	public static function logDebug($message) { self::rawLog('debug', $message, self::DEBUG); }

	public static function logError($message) { self::log('error', $message, self::GDO_ERROR); }

	public static function logMessage($message) { self::log('message', $message, self::GDO_MESSAGE); }

	public static function logWarning($message) { self::log('warning', $message, self::GDO_WARNING); }

	public static function logCritical($message)
	{
		self::log('critical', $message, self::GDO_CRITICAL);
	}

	public static function logException(Throwable $e)
	{
		$message = sprintf("%s in %s Line %s\n", $e->getMessage(), Debug::shortpath($e->getFile()), $e->getLine());
		self::log('critical', $message, self::GDO_CRITICAL);
		$log = Debug::backtraceException($e, true) . PHP_EOL . self::stripPassword($_REQUEST) . PHP_EOL . $message;
		self::log('critical', $log, self::GDO_CRITICAL);
	}

	public static function logInstall($message) { self::log('install', $message, self::_NONE); }

	public static function logHTTP($message) { self::rawLog('http', $message, self::HTTP_ERROR); }

}

deff('GDO_TIMEZONE', ini_get('date.timezone')); #PP#delete#
deff('GDO_LOG_REQUEST', false); #PP#delete#
