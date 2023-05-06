<?php
declare(strict_types=1);
namespace GDO\Date;

use DateTime;
use DateTimeZone;
use GDO\Core\Application;
use GDO\Core\GDO_Exception;
use GDO\DB\Cache;
use GDO\Language\Trans;

/**
 * Time helper class.
 * Using dates with milliseconds.
 *
 * For GDT_Timestamp, the value is the microtime(true) timestamp.
 * For GDT_Date and GDT_DateTime, the value is a \DateTime object.
 * The $var is always a mysql date string in UTC.
 *
 * There are 3 time datatypes the class operates on.
 *  - $time(stamp): A float, microtime(true)
 *  - $date: A string, date($format_via_trans)
 *  - $datetime: A PHP @\DateTime object.
 *
 * PHP Timezone will be set to UTC.
 *
 * @TODO: Sometimes functions take a formatstring sometimes a formatname t(df_). Always use formatstring. fix all bugs.
 *
 * @version 7.0.3
 * @since 2.0.0
 * @author gizmore
 * @see GDT_Week
 * @see GDT_Date
 * @see GDT_DateTime
 * @see GDT_Timestamp
 * @see GDT_Duration
 */
final class Time
{

	final public const ONE_MILLISECOND = 0.001;
	final public const ONE_SECOND = 1;
	final public const ONE_MINUTE = 60;
	final public const ONE_HOUR = 3600;
	final public const ONE_DAY = 86400;
	final public const ONE_WEEK = 604800;
	final public const ONE_MONTH = 2629800;
	final public const ONE_YEAR = 31557600;

	# known display formats from lang file
	final public const FMT_MINUTE = 'minute';
	final public const FMT_SHORT = 'short';
	final public const FMT_LONG = 'long';
	final public const FMT_DAY = 'day'; # @TODO: Date format FMT_DAY is same as FMT_SHORT.
	final public const FMT_MS = 'ms';
	final public const FMT_DB = 'db';

	################
	### Timezone ###
	################
	/**
	 * UTC DB ID. UTC is always '1'.
	 */
	final public const UTC = '1';

	final public const MONDAY = '1';

	final public const TUESDAY = '2';

	final public const WEDNESDAY = '3';

	final public const THURSDAY = '4';

	final public const FRIDAY = '5';
	final public const SATURDAY = '6';
	final public const SUNDAY = '7';

	public static DateTimeZone $UTC;

	/**
	 * The timezone as GDO db row id.
	 */
	public static string $TIMEZONE = self::UTC;

	###############
	### Convert ###
	###############
	/**
	 * @var DateTimeZone[]
	 */
	public static array $TIMEZONE_OBJECTS = [];

	/**
	 * @var GDO_Timezone[]
	 */
	private static array $TZ_CACHE;

	public static function setTimezoneNamed(string $timezoneName): void
	{
		self::setTimezoneGDO(GDO_Timezone::getByName($timezoneName));
	}

	public static function setTimezoneGDO(GDO_Timezone $tz): void
	{
		self::setTimezone($tz->getID());
	}

	public static function setTimezone(string $timezoneId): void
	{
		self::$TIMEZONE = $timezoneId;
	}

	public static function getDateDay(int|float|null $time = 0): ?string
	{
		return self::getDate($time, 'Y-m-d');
	}

	/**
	 * Get a mysql date from a timestamp, like YYYY-mm-dd HH:ii:ss.vvv.
	 */
	public static function getDate(int|float|null $time = 0, string $format = 'Y-m-d H:i:s.v'): ?string
	{
		if ($dt = self::getDateTime($time))
		{
			return $dt->format($format);
		}
		return null;
	}

	/**
	 * Get a datetime object from a timestamp.
	 */
	public static function getDateTime(float|int|null $time=0): ?DateTime
	{
		if ($time === null)
		{
			return null;
		}
		$time = $time <= 0 ? Application::$MICROTIME : $time;
		return DateTime::createFromFormat('U.u', sprintf('%.06f', $time), self::$UTC);
	}

	public static function getDateSec(float|int $time=0): ?string
	{
		return self::getDate($time, 'Y-m-d H:i:s');
	}

	public static function getDateWithoutTime(float|int|null $time=0): ?string
	{
		if ($date = self::getDate($time))
		{
			return substr($date, 0, 10);
		}
		return null;
	}

	/**
	 * @throws GDO_Exception
	 */
	public static function parseDateDB(?string $date): float
	{
		return self::parseDate($date, self::UTC, 'db');
	}

	/**
	 * Convert DateTime input from a user.
	 * This is usually in the users language format and timezone
	 *
	 * @throws GDO_Exception
	 */
	public static function parseDate(?string $date, string $timezone = null, string $format = 'parse'): ?float
	{
		return self::parseDateIso(Trans::$ISO, $date, $timezone, $format);
	}

	###############
	### Display ###
	###############
	/**
	 * Convert a user date input to a timestamp.
	 *
	 * @throws GDO_Exception
	 */
	public static function parseDateIso(string $iso, ?string $date, string $timezone = null, string $format = 'parse'): ?float
	{
		if ($d = self::parseDateTimeISO($iso, $date, $timezone, $format))
		{
			return (float) $d->format('U.u');
		}
		return null;
	}

	/**
	 * Parse a string into a datetime.
	 *
	 * @throws GDO_Exception
	 */
	public static function parseDateTimeISO(string $iso, ?string $date, string $timezone = null, string $format = 'parse'): ?DateTime
	{
		if (!$date)
		{
			return null;
		}

		$date = preg_replace('/[ap]m/i', '', $date);
		$date = trim($date);

		$len = strlen($date);
		if ($len === 10)
		{
			$date .= ' 00:00:00.000';
		}
		elseif ($len === 16)
		{
			$date .= ':00.000';
		}
		elseif ($len === 19)
		{
			$date .= '.000';
		}
		elseif ($len !== 23)
		{
			throw new GDO_Exception('cannot parse invalid date format.');
		}

		# Parse
		if ($format === 'db')
		{
			$format = 'Y-m-d H:i:s.v';
		}
		else
		{
			$format = tiso($iso, 'df_' . $format);
		}
		$timezone = $timezone ?: self::$TIMEZONE;
		$timezone = self::getTimezoneObject($timezone);
		return DateTime::createFromFormat($format, $date, $timezone);
	}

	public static function getTimezoneObject(string $timezoneId = null): DateTimeZone
	{
		return $timezoneId === null ?
			self::$UTC :
			self::_getTZCached($timezoneId);
	}

	/**
	 * @throws \Exception
	 */
	private static function _getTZCached(string $timezoneId): DateTimeZone
	{
		if (isset(self::$TIMEZONE_OBJECTS[$timezoneId]))
		{
			return self::$TIMEZONE_OBJECTS[$timezoneId];
		}

		if (!isset(self::$TZ_CACHE))
		{
			$key = 'gdo_timezones';
			if (null === ($cache = Cache::get($key)))
			{
				if ($cache = GDO_Timezone::table()->allCached())
				{
					Cache::set($key, $cache);
				}
			}
			self::$TZ_CACHE = $cache;
		}
		$timezone = self::$TZ_CACHE[$timezoneId];
		self::$TZ_CACHE[$timezoneId] = $tz = new DateTimeZone($timezone->getName());
		return $tz;
	}

	/**
	 * @throws GDO_Exception
	 */
	public static function parseDateTime(?string $date, string $timezone = null, string $format = 'parse'): ?DateTime
	{
		return self::parseDateTimeISO(Trans::$ISO, $date, $timezone, $format);
	}

	/**
	 * Display a timestamp.
	 */
	public static function displayTimestamp(int|float $timestamp, string $format = 'short', string $default_return = '---',string $timezone = null): string
	{
		return self::displayTimestampISO(Trans::$ISO, $timestamp, $format, $default_return, $timezone);
	}

	public static function displayTimestampISO(string $iso, int|float $timestamp, string $format = 'short', string $default_return = '---', string $timezone = null): string
	{
		if ($timestamp <= 0)
		{
			return $default_return;
		}
		$dt = DateTime::createFromFormat('U.u', sprintf('%.06f', $timestamp), self::$UTC);
		return self::displayDateTimeISO($iso, $dt, $format, $default_return, $timezone);
	}

	/**
	 * Display a localized datetime.
	 */
	public static function displayDateTimeISO(string $iso, DateTime $datetime = null, $format = 'short', ?string $default_return = '---', string $timezoneId = null): string
	{
		$format = tiso($iso, "df_$format");
		return self::displayDateTimeFormat(
			$datetime, $format, $default_return, $timezoneId);
	}

	###########
	### Age ###
	###########

	/**
	 * Display a formatted datetime.
	 */
	public static function displayDateTimeFormat(DateTime $datetime = null, string $format = 'Y-m-d H:i:s.v', ?string $default_return = '---', string $timezoneId = null): string
	{
		if (!$datetime)
		{
			return $default_return;
		}
		$timezoneId = $timezoneId ?: self::$TIMEZONE;
		$datetime->setTimezone(self::getTimezoneObject($timezoneId));
		return $datetime->format($format);
	}

	/**
	 * Display a datetime string.
	 */
	public static function displayDate(string $date = null, string $format = 'short', string $default_return = '---', string $timezone = null): string
	{
		return self::displayDateISO(Trans::$ISO, $date, $format, $default_return, $timezone);
	}

	/**
	 * Display a datestring.
	 */
	public static function displayDateISO(string $iso, string $date = null, string $format = 'short', string $default_return = '---', string $timezone = null): string
	{
		if ($date === null)
		{
			return $default_return;
		}
		if (!($d = self::parseDateTimeDB($date)))
		{
			return $default_return;
		}
		return self::displayDateTimeISO($iso, $d, $format, $default_return, $timezone);
	}

	/**
	 * Parse a date from user input in user timezone, but Y-m-d format.
	 *
	 * @throws GDO_Exception
	 */
	public static function parseDateTimeDB(?string $date, ?string $timezone = self::UTC): ?DateTime
	{
		return self::parseDateTimeISO('en', $date, $timezone, 'db');
	}

	public static function displayDateTime(DateTime $datetime = null, string $format = 'short', string $default_return = '---', string $timezone = null): string
	{
		return self::displayDateTimeISO(Trans::$ISO, $datetime, $format, $default_return, $timezone);
	}

	public static function displayTimeISO(string $iso, $time = null, string $format = 'short', string $default_return = '---', string $timezoneId = null): string
	{
		$dt = self::getDateTime($time);
		return self::displayDateTimeISO($iso, $dt, $format, $default_return, $timezoneId);
	}

	/**
	 * Get date diff in seconds. @TODO make it two args for two dates, default now.
	 */
	public static function getDiff(string $date): float
	{
		$b = new DateTime(self::getDate(Application::$MICROTIME));
		$a = new DateTime($date);
		return abs($b->getTimestamp() - $a->getTimestamp());
	}

	/**
	 * Get the timestamp for a database date (UTC).
	 */
	public static function getTimestamp(?string $date): int|float
	{
		return $date ?
			self::parseDate($date, self::UTC, 'db') :
			Application::$MICROTIME;
	}

	#################
	### From Week ###
	#################

	/**
	 * Get the age in years of a date.
	 */
	public static function getAge(?string $date): float
	{
		$seconds = self::getAgo($date);
		return $seconds / self::ONE_YEAR;
	}

	################
	### Duration ###
	################

	/**
	 * Get the age of a date in seconds.
	 */
	public static function getAgo(?string $date): float
	{
		return $date ?
			Application::$MICROTIME - self::getTimestamp($date) :
			0;
	}

	public static function getAgeTS(int|float $duration): float
	{
		return $duration / self::ONE_YEAR;
	}

	public static function displayAge(?string $date): string
	{
		return self::displayAgeTS(self::getTimestamp($date));
	}

	public static function displayAgeTS(int|float $timestamp): string
	{
		$timestamp = Application::$MICROTIME - $timestamp;
		return self::humanDuration($timestamp);
	}

	/**
	 * Return a human readable duration.
	 * Example: 666 returns 11 minutes 6 seconds.
	 */
	public static function humanDuration(float|int|null $seconds, int $nUnits = 2, bool $withMillis = false): string
	{
		return self::humanDurationISO(Trans::$ISO, $seconds, $nUnits, $withMillis);
	}

	########################
	### Human to seconds ###
	########################

	public static function humanDurationISO(string $iso, int|float|null $seconds, int $nUnits = 2, bool $withMillis = false): string
	{
// 		static $cache = [];
// 		if (!isset($cache[$iso]))
// 		{
		$cache[$iso] = [
// 				tiso($iso, 'tu_ms') => 1000,
			tiso($iso, 'tu_s') => 60,
			tiso($iso, 'tu_m') => 60,
			tiso($iso, 'tu_h') => 24,
			tiso($iso, 'tu_d') => 7,
			tiso($iso, 'tu_w') => 53,
// 				tiso($iso, 'tu_mo') => 4,
			tiso($iso, 'tu_y') => 1000000,
		];
// 		}
		return self::humanDurationRaw($seconds, $nUnits, $cache[$iso], $withMillis);
	}

	#############
	### Parts ###
	#############

	public static function humanDurationRaw(int|float|null $seconds, int $nUnits, array $units, bool $withMillis = false): string
	{
		$calced = [];
		if ($withMillis)
		{
			$ms = intval($seconds * 1000) % 1000;
		}
		$duration = (int)$seconds;
		foreach ($units as $text => $mod)
		{
			if (0 < ($remainder = $duration % $mod))
			{
				$calced[] = $remainder . $text;
			}
			$duration = intval($duration / $mod);
			if ($duration === 0)
			{
				break;
			}
		}

		if (count($calced) === 0)
		{
			return '0' . key($units);
		}

		$calced = array_reverse($calced, true);
		$i = 0;
		foreach (array_keys($calced) as $key)
		{
			$i++;
			if ($i > $nUnits)
			{
				unset($calced[$key]);
			}
		}

		if (count($calced) < $nUnits)
		{
			if ($withMillis)
			{
				$calced[] = sprintf('%.03d', $ms);
			}
		}

		return implode(' ', $calced);
	}

	public static function displayAgeISO(string $date, string $iso): string
	{
		return self::displayAgeTSISO(self::getTimestamp($date), $iso);
	}

	public static function displayAgeTSISO(int|float $timestamp, string $iso): string
	{
		return self::humanDurationISO($iso, Application::$TIME - $timestamp);
	}

	##############
	### Is-Day ###
	##############

	/**
	 * @throws \Exception
	 */
	public static function weekTimestamp(string|int $year, string|int $week): int
	{
		$week_start = new DateTime('now', Time::$UTC);
		$week_start->setISODate(intval($year), intval($week));
		$week_start = $week_start->format('U');
		return (int) $week_start;
	}

	public static function humanDurationEN(float|int $seconds, int $nUnits = 2, bool $withMillis = false): string
	{
		return self::humanDurationISO('en', $seconds, $nUnits);
	}

	public static function isValidDuration(string $string, int|float|null $min, int|float|null $max): bool
	{
		$seconds = self::humanToSeconds($string);
		if (!is_numeric($seconds))
		{
			return false;
		}
		if (($min !== null) && ($seconds < $min))
		{
			return false;
		}
		if (($max !== null) && ($seconds > $max))
		{
			return false;
		}
		return true;
	}

	/**
	 * Convert a human duration to seconds.
	 *
	 * Input may be like 3d5h8m 7s.
	 * @TODO Also possible will be 1 month 3 days or 1year2sec.
	 * No unit means default unit, which is seconds.
	 *
	 * Supported units are:
	 *
	 *  - ms, millis, millisecond,
	 *  - s, sec, second, seconds,
	 *  - m, min, minute, minutes,
	 *  - h, hour, hours,
	 *  - d, day, days,
	 *  - w, week, weeks,
	 *  - mo, month, months,
	 *  - y, year, years.
	 * */
	public static function humanToSeconds(?string $duration): float|null
	{
		if ($duration === null)
		{
			return null;
		}
		$matches = null;
		if (!preg_match_all('/(?:([0-9]+)\\s*([smhdwoy]{0,2}))+/i', $duration, $matches))
		{
			return 0.0;
		}
		$multis = [
			'ms' => 0.001,
			's' => 1,
			'm' => 60,
			'h' => 3600,
			'd' => 86400,
			'w' => 604800,
			'mo' => 2592000,
			'y' => 31536000,
		];
		$back = 0.0;
		$len = count($matches[1]);
		for ($i = $j = 0; $i < $len; $i++, $j++)
		{
			$d = floatval($matches[1][$j]);
			$unit = $multis[$matches[2][$j]] ?? 1.0;
			$back += $d * $unit;
		}
		return floatval($back);
	}

	public static function getYear(string $date): string { return substr($date, 0, 4); }

	public static function getMonth(string $date): string { return substr($date, 5, 2); }

	public static function getDay(string $date): string { return substr($date, 8, 2); }

	/**
	 * Is a timestamp a Sunday (UTC)
	 */
	public static function isSunday($time = 0, string $timezoneId = self::UTC): bool
	{
		return self::isDay(self::SUNDAY, $time, $timezoneId);
	}

	public static function isDay(string $day, $time = 0, string $timezoneId = self::UTC): bool
	{
		$time = $time ?: Application::$MICROTIME;
		$dt = Time::getDateTime($time);
		return self::displayDateTimeFormat($dt, 'N', Time::MONDAY) === $day;
	}

}

Time::$UTC = new DateTimeZone('UTC');
Time::$TIMEZONE_OBJECTS[Time::UTC] = Time::$UTC;
date_default_timezone_set('UTC');
