<?php
namespace GDO\Util;

use GDO\Date\Time;
use GDO\Language\Trans;
use GDO\User\GDO_User;

/**
 * Call a function wrapped in a locale change.
 * Execute callbacks with the locale of a user.
 *
 * @author gizmore
 */
final class Localized
{

	public static function forUser(GDO_User $user, $callback)
	{
		$old = Time::$TIMEZONE;
		Time::setTimezone($user->getTimezone());
		$result = self::withISO($user->getLangISO(), $callback);
		Time::setTimezone($old);
		return $result;
	}

	public static function withISO(string $iso, $callback)
	{
		$old = Trans::$ISO;
		Trans::setISO($iso);
		$result = $callback();
		Trans::setISO($old);
		return $result;
	}

}
