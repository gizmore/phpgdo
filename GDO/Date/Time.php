<?php
namespace GDO\Date;

use DateTime;
use DateTimeZone;
use GDO\Core\Application;
use GDO\Core\GDO_Error;
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
 * @version 7.0.2
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

	# durations in seconds
	public const ONE_MILLISECOND = 0.001;
	public const ONE_SECOND = 1;
	public const ONE_MINUTE = 60;
	public const ONE_HOUR = 3600;
	public const ONE_DAY = 86400;
	public const ONE_WEEK = 604800;
	public const ONE_MONTH = 2629800;
	public const ONE_YEAR = 31557600;

	# known display formats from lang file
	public const FMT_MINUTE = 'minute';
	public const FMT_SHORT = 'short';
	public const FMT_LONG = 'long';
	public const FMT_DAY = 'day'; # @TODO: Date format FMT_DAY is same as FMT_SHORT.
	public const FMT_MS = 'ms';
	public const FMT_DB = 'db';

	################
	### Timezone ###
	################
	/**
	 * UTC DB ID. UTC is always '1'.
	 *
	 * @see GDO_Timezone
	 */
	public const UTC = '1';
	public const MONDAY = '1'; # UTC Timezone object
	public const TUESDAY = '2'; # default timezone id
	public const WEDNESDAY = '3';
	public const THURSDAY = '4'; # Internal cache
	public const FRIDAY = '5';
	public const SATURDAY = '6';
	public const SUNDAY = '7';
	public static $UTC;
	/**
	 * The timezone as GDO db row id.
	 *
	 * @var string
	 */
	public static string $TIMEZONE = self::UTC;

	###############
	### Convert ###
	###############
	/**
	 * Timezone object cache: @TODO Remove?
	 *
	 * @var DateTimeZone[]
	 */
	public static array $TIMEZONE_OBJECTS = [];
	private static array $TZ_CACHE;

	public static function setTimezoneNamed(string $timezoneName): void
	{
		self::setTimezoneGDO(GDO_Timezone::getByName($timezoneName));
	}

	public static function setTimezoneGDO(GDO_Timezone $tz): void
	{
		self::setTimezone($tz->getID());
	}

	public static function setTimezone(string $timezoneId)
	{
		#PP#start#
		if (!is_numeric($timezoneId))
		{
			die('FATAL');
			xdebug_break();
		}
		#PP#end#
		self::$TIMEZONE = $timezoneId;
	}

	public static function getDateDay($time = 0)
	{
		return self::getDate($time, 'Y-m-d');
	}

	/**
	 * Get a mysql date from a timestamp, like YYYY-mm-dd HH:ii:ss.vvv.
	 *
	 * @return string
	 * @example $date = Time::getDate();
	 */
	public static function getDate($time = 0, $format = 'Y-m-d H:i:s.v')
	{
		if ($dt = self::getDateTime($time))
		{
			$date = $dt->format($format);
			return $date;
		}
	}

	/**
	 * Get a datetime object from a timestamp.
	 *
	 * @param number $time
	 */
	public static function getDateTime($time = 0): DateTime
	{
		$time = $time <= 0 ? Application::$MICROTIME : (float)$time;
		return DateTime::createFromFormat('U.u', sprintf('%.06f', $time), self::$UTC);
	}

	public static function getDateSec($time = 0)
	{
		return self::getDate($time, 'Y-m-d H:i:s');
	}

	public static function getDateWithoutTime($time = null): string
	{
		return substr(self::getDate($time), 0, 10);
	}

	public static function parseDateDB($date): float
	{
		return self::parseDate($date, self::UTC, 'db');
	}

	/**
	 * Convert DateTime input from a user.
	 * This is usually in the users language format and timezone
	 *
	 * @param string $date
	 * @param string $timezone
	 * @param string $format
	 *
	 * @return int Timestamp
	 */
	public static function parseDate($date, $timezone = null, $format = 'parse'): float
	{
		$timestamp = self::parseDateIso(Trans::$ISO, $date, $timezone, $format);
		return $timestamp;
	}

	###############
	### Display ###
	###############

	/**
	 * Convert a user date input to a timestamp.
	 *
	 * @TODO parseDateIso is broken a bit, because strlen($date) might differ across languages.
	 *
	 * @param string $iso
	 * @param string $date
	 * @param string $timezone
	 * @param string $format
	 *
	 * @return int Timestamp
	 */
	public static function parseDateIso($iso, $date, $timezone = null, $format = 'parse'): float
	{
		if ($d = self::parseDateTimeISO($iso, $date, $timezone, $format))
		{
			$timestamp = $d->format('U.u');
			return (float)$timestamp;
		}
	}

	/**
	 * Parse a string into a datetime.
	 *
	 * @param string $date
	 * @param int $timezone
	 * @param string $format
	 * @param string $iso
	 *
	 * @throws GDO_Error
	 * @return DateTime
	 */
	public static function parseDateTimeISO($iso, $date, $timezone = null, $format = 'parse')
	{
		# Adjust
		if (!$date)
		{
			return null;
		}

		$date = preg_replace('/[ap]m/iD', '', $date);
// 	    $date = preg_replace('/ {2,}/D', ' ', $date);
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
		$timezone = $timezone ? $timezone : self::$TIMEZONE;
		$timezone = self::getTimezoneObject($timezone);
		if (!($d = DateTime::createFromFormat($format, $date, $timezone)))
		{
			throw new GDO_Error('err_invalid_date', [html($date), $format]);
		}
		return $d;
	}

	public static function getTimezoneObject(string $timezoneId = null)
	{
		return $timezoneId === null ?
			self::$TIMEZONE :
			self::_getTZCached($timezoneId);
	}

	private static function _getTZCached(string $timezoneId)
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

	public static function parseDateTime($date, $timezone = null, $format = 'parse')
	{
		return self::parseDateTimeIso(Trans::$ISO, $date, $timezone, $format);
	}

	/**
	 * Display a timestamp.
	 *
	 * @param $timestamp
	 * @param $langid
	 * @param $default_return
	 *
	 * @return string
	 */
	public static function displayTimestamp($timestamp, $format = 'short', $default_return = '---', $timezone = null)
	{
		return self::displayTimestampISO(Trans::$ISO, $timestamp, $format, $default_return, $timezone);
	}

	public static function displayTimestampISO($iso, $timestamp, $format = 'short', $default_return = '---', $timezone = null)
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
		$timezoneId = $timezoneId ? $timezoneId : self::$TIMEZONE;
		$datetime->setTimezone(self::getTimezoneObject($timezoneId));
		return $datetime->format($format);
	}

	/**
	 * Display a datetime string.
	 *
	 * @param string $date
	 * @param string $format
	 * @param string $default_return
	 * @param int $timezone
	 *
	 * @return string
	 */
	public static function displayDate($date = null, $format = 'short', $default_return = '---', $timezone = null)
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
	 */
	public static function parseDateTimeDB(?string $date, $timezone = self::UTC): ?DateTime
	{
		return self::parseDateTimeIso('en', $date, $timezone, 'db');
	}

	public static function displayDateTime(DateTime $datetime = null, string $format = 'short', string $default_return = '---', int $timezone = null)
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
	public static function getDiff($date): int
	{
		$b = new DateTime(self::getDate(Application::$MICROTIME));
		$a = new DateTime($date);
		return abs($b->getTimestamp() - $a->getTimestamp());
	}

	/**
	 * Get the timestamp for a database date (UTC).
	 *
	 * @param string $date
	 *
	 * @return float microtime (ms)
	 */
	public static function getTimestamp($date = null)
	{
		$ts = $date ? self::parseDate($date, self::UTC, 'db') : Application::$MICROTIME;
		return $ts;
	}

	#################
	### From Week ###
	#################

	/**
	 * Get the age in years of a date.
	 */
	public static function getAge(string $date = null): float
	{
		if ($seconds = self::getAgo($date))
		{
			return $seconds / self::ONE_YEAR;
		}
		return 0.0;
	}

	################
	### Duration ###
	################

	/**
	 * Get the age of a date in seconds.
	 */
	public static function getAgo(string $date = null): float
	{
		return $date ?
			Application::$MICROTIME - self::getTimestamp($date) :
			0;
	}

	public static function getAgeTS(int $duration)
	{
		return $duration / self::ONE_YEAR;
	}

	public static function displayAge($date)
	{
		return self::displayAgeTS(self::getTimestamp($date));
	}

	public static function displayAgeTS($timestamp)
	{
		$timestamp = Application::$TIME - (int)$timestamp;
		return self::humanDuration($timestamp);
	}

	/**
	 * Return a human readable duration.
	 * Example: 666 returns 11 minutes 6 seconds.
	 *
	 * @TODO: Time::humanDuration() shall support ms.
	 *
	 * @param $duration int in seconds.
	 * @param $nUnits int how many units to display max.
	 *
	 * @return string
	 */
	public static function humanDuration($seconds, int $nUnits = 2, bool $withMillis = false)
	{
		return self::humanDurationISO(Trans::$ISO, $seconds, $nUnits, $withMillis);
	}

	########################
	### Human to seconds ###
	########################

	public static function humanDurationISO(string $iso, $seconds, int $nUnits = 2, bool $withMillis = false): string
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

	/**
	 * @param int|float $seconds
	 */
	public static function humanDurationRaw($seconds, int $nUnits, array $units, bool $withMillis = false)
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
			$duration = intval($duration / $mod, 10);
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

	public static function displayAgeISO($date, $iso)
	{
		return self::displayAgeTSISO(self::getTimestamp($date), $iso);
	}

	public static function displayAgeTSISO($timestamp, $iso)
	{
		return self::humanDurationISO($iso, Application::$TIME - $timestamp);
	}

	##############
	### Is-Day ###
	##############

	public static function weekTimestamp($year, $week)
	{
		$week_start = new DateTime('now', Time::$UTC);
		$week_start->setISODate(intval($year, 10), intval($week, 10));
		$week_start = $week_start->format('U');
		return $week_start;
	}

	public static function humanDurationEN($seconds, int $nUnits = 2, bool $withMillis = false): string
	{
		return self::humanDurationISO('en', $seconds, $nUnits);
	}

	/**
	 * @param int|float|null $min
	 * @param int|float|null $max
	 */
	public static function isValidDuration(string $string, $min = null, $max = null): bool
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
	 * Also possible is 1 month 3 days or 1year2sec.
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
	 *
	 * @param $duration string is the duration in human format.
	 *
	 * @return float|int|null - duration in seconds
	 * */
	public static function humanToSeconds(string $duration)
	{
		if (is_int($duration))
		{
			return $duration;
		}
		if (!is_string($duration))
		{
			return 0.0;
		}
		if (is_numeric($duration))
		{
			return floatval($duration);
		}
		$matches = null;
		if (!preg_match_all('/(?:([0-9]+)\\s*([smhdwoy]{0,2}))+/Di', $duration, $matches))
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
			if ($d)
			{
				if ($unit = @$multis[$matches[2][$j]])
				{
					$back += $d * $unit;
				}
				else
				{
					$back += $d;
				}
			}
		}
		return $back;
	}

	public static function getYear(string $date) { return substr($date, 0, 4); }

	public static function getMonth(string $date) { return substr($date, 5, 2); }

	public static function getDay(string $date) { return substr($date, 8, 2); }

	/**
	 * Is a timestamp a Sunday (UTC)
	 */
	public static function isSunday($time = 0, string $timezoneId = self::UTC): bool
	{
		return self::isDay(self::SUNDAY, $time, $timezoneId);
	}

	public static function isDay(string $day, $time = 0, string $timezoneId = self::UTC): bool
	{
		$time = $time ? $time : Application::$MICROTIME;
		$dt = Time::getDateTime($time);
		return self::displayDateTimeFormat($dt, 'N', Time::MONDAY,/**UTC**/) === $day;
	}

}

Time::$UTC = new DateTimeZone('UTC');
Time::$TIMEZONE_OBJECTS[Time::UTC] = Time::$UTC;
date_default_timezone_set('UTC');
