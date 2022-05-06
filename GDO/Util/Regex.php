<?php
namespace GDO\Util;

/**
 * Regex utility.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Regex
{
	/**
	 * Return the first match of a capturing regex.
	 */
	public static function firstMatch(string $pattern, string $s) : string
	{
		$matches = null;
		if (preg_match($pattern, $s, $matches))
		{
			if (isset($matches[1]))
			{
				return $matches[1];
			}
		}
		return null;
	}
	
}
