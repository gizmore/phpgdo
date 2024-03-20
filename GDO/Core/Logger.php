<?php
declare(strict_types=1);
namespace GDO\Core;

use DateTimeZone;
use GDO\Date\Time;
use GDO\Form\GDT_Form;
use GDO\Util\FileUtil;
use Throwable;

/**
 * The GDO Logger.
 * Buffered.
 * One logfile per user.
 *
 * @version 7.0.3
 * @since 1.0.0
 * @author gizmore
 * @author spaceone
 */
final class Logger
{

	final public const GDO_WARNING = 0x01;
	final public const GDO_MESSAGE = 0x02;
	final public const GDO_ERROR = 0x04;
	final public const GDO_CRITICAL = 0x08;
	final public const PHP_ERROR = 0x10;
	final public const DB_ERROR = 0x20;
	final public const HTTP_ERROR = 0x80;
	final public const HTTP_GET = 0x100;
	final public const HTTP_POST = 0x200;

	final public const IP = 0x400;
	final public const BUFFERED = 0x1000;

	final public const DEBUG = 0x2000;

	final public const ALL = 0x37ff;

	public static int $WRITES = 0;

	public static string $POST_DELIMITER = '.::.';

	public static ?DateTimeZone $TIMEZONE;

	private static ?string $username;

	private static string $basedir = GDO_PATH . 'protected/logs';

	private static int $logbits = self::ALL;

	private static string $logformat = "%s [%s%s] - %s\n";

	private static array $logs = [];

	/**
	 * Init the logger. If a username is given, the logger will log into a logs/username dir.
	 */
	public static function init(string $username = null, int $logbits = self::ALL, string $basedir = 'protected/logs'): void
	{
		self::$username = $username;
		self::$logbits = $logbits;
		self::$basedir = GDO_PATH . $basedir;
		self::$TIMEZONE = new DateTimeZone(GDO_TIMEZONE);
	}

//	public static function setLogFormat($format): void { self::$logformat = $format; }
//
//	public static function enableBuffer(): void { self::enable(self::BUFFERED); }

	public static function enable($bits): void { self::$logbits |= $bits; }

	public static function disable($bits): void { self::$logbits &= ~$bits; }

	public static function disableBuffer()
	{
		self::disable(self::BUFFERED);
		self::flush();
	}

	/**
	 * Flush all logfiles.
	 */
	public static function flush(): void
	{
		foreach (self::$logs as $file => $msg)
		{
			if (self::writeLog($file, $msg))
			{
				unset(self::$logs[$file]);
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
			return false;
		}

		# Default kill banner.
		if (!is_file($filename))
		{
			$bool =  false !== file_put_contents($filename, '<?php die(2); ?>' . PHP_EOL);
			$bool = $bool && chmod($filename, GDO_CHMOD & 0666);
			if (!$bool)
			{
				return false;
			}
		}

		# Write to file
		if (!file_put_contents($filename, $message, FILE_APPEND))
		{
			return false;
		}

		return true;
	}

	/**
	 * Get the full log path, either for username log or site log.
	 */
	private static function getFullPath(string $filename, string $username = null): string
	{
		$date = Time::getDate(0, 'Ymd');
		return $username
			? sprintf('%s/memberlog/%s/%s_%s.txt', self::$basedir, $username, $date, $filename)
			: sprintf('%s/%s_%s.txt', self::$basedir, $date, $filename);
	}

	/**
	 * Recursively create logdir with GDO_CHMOD permissions.
	 */
	private static function createLogDir(string $filename): bool
	{
		return !!FileUtil::createdDir(dirname($filename));
	}


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
			$time = Time::displayTimestamp(0, 'db');
			$ip = self::isDisabled(self::IP) ? '' : @$_SERVER['REMOTE_ADDR'];
			$username = self::$username ?: ':~guest~';
			self::logB($filename, sprintf(self::$logformat, $time, $ip, $username, $message));
		}
	}

	########################
	### Default logfiles ###
	########################

	public static function isEnabled(int $bits): bool
	{
		return $bits === (self::$logbits & $bits);
	}

	public static function isDisabled(int $bits): bool
	{
		return !self::isEnabled($bits);
	}

	private static function logB(string $filename, string $message): void
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

	public static function isBuffered(): bool
	{
		return self::isEnabled(self::BUFFERED);
	}

	/**
	 * Get the whole request to log it. Censor passwords.
	 */
	private static function getRequest(): string
	{
		$back = $_SERVER['REQUEST_METHOD'];
		$back .= ' ';
		$back .= urldecode($_SERVER['REQUEST_URI']);
		if ($_SERVER['REQUEST_METHOD'] === GDT_Form::POST)
		{
			$back .= self::$POST_DELIMITER . 'POSTDATA: ' . self::stripPassword($_REQUEST);
		}
		return $back;
	}

	/**
	 * strip values from arraykeys which contain 'pass'
	 */
	private static function stripPassword(array $a): string
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
			$back .= self::$POST_DELIMITER . $k . ' => ' . $v;
		}
		return self::shortString($back);
	}

	/**
	 * shorten a string and remove dangerous pattern
	 */
	public static function &shortString(string $str, int $length = 256): string
	{
		$str = mb_substr($str, 0, $length);
		$str = str_replace('<?', '##', $str);
		return $str;
	}

	public static function logCron(string $message): void
	{
		self::rawLog('cron', $message);
		echo $message . "\n";
	}

	public static function rawLog(string $filename, string $message, int $logmode = 0): void
	{
		# log it?
		if (self::isEnabled($logmode))
		{
			self::logB($filename, $message . PHP_EOL);
		}
	}

	public static function logWebsocket(string $message): void
	{
		self::rawLog('websocket', $message,  self::$logbits);
	}

	public static function logDebug(string $message): void
	{
		self::rawLog('debug', $message, self::DEBUG);
	}

	public static function logError(string $message): void
	{
		self::log('error', $message, self::GDO_ERROR);
	}

	public static function logMessage(string $message): void
	{
		self::log('message', $message, self::GDO_MESSAGE);
	}

	public static function logWarning(string $message): void
	{
		self::log('warning', $message, self::GDO_WARNING);
	}

	public static function logCritical(string $message): void
	{
		self::log('critical', $message, self::GDO_CRITICAL);
	}

	public static function logException(Throwable $e): void
	{
		$message = sprintf("%s in %s Line %s\n", $e->getMessage(), Debug::shortpath($e->getFile()), $e->getLine());
		self::log('critical', $message, self::GDO_CRITICAL);
		$log = Debug::backtraceException($e, false) . PHP_EOL . self::stripPassword($_REQUEST);
		self::log('critical', $log, self::GDO_CRITICAL);
	}

}

deff('GDO_TIMEZONE', ini_get('date.timezone')); #PP#delete#
deff('GDO_LOG_REQUEST', false); #PP#delete#
