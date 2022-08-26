<?php
namespace GDO\Util;

/**
 * Array utility.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.5.0
 */
final class Arrays
{
	public static function arrayed($value)
	{
		if (is_array($value))
		{
			return $value;
		}
		return $value === null ? [] : [$value];
	}
	
	/**
	 * Fixed explode with no elements on empty string.
	 */
	public static function explode(string $string, string $delimiter=',') : array
	{
		return $string === '' ? [] : explode($delimiter, $string);
	}
	
	/**
	 * Recursive implode. Code taken from php.net.
	 * Original code by: kromped@yahoo.com
	 * @param string[] $pieces
	 */
	public static function implode(string $glue, array $pieces, array $retVal=[]) : string
	{
		foreach ($pieces as $r_pieces)
		{
			$retVal[] = is_array($r_pieces) ? '['.self::implode($glue, $r_pieces).']' : $r_pieces;
		}
		return implode($glue, $retVal);
	}
	
	/**
	 * Reverse an array but keep keys.
	 */
	public static function reverse(array $array) : array
	{
		$k = array_keys($array);
		$v = array_values($array);
		$rv = array_reverse($v);
		$rk = array_reverse($k);
		return array_combine($rk, $rv);
	}
	
	/**
	 * Return comma seperated and a final "and" like foo, bar and boo.
	 */
	public static function implodeHuman(array $array) : string
	{
		switch (count($array))
		{
			case 0: return '';
			case 1: return array_pop($array);
			default:
				$last = array_pop($array);
				return implode(', ', $array) . ' ' . t('and') . ' ' . $last;
		}
	}
	
	/**
	 * Unique array filter that respect same objects as equal.
	 */
	public static function unique(array $array) : array
	{
		$unique = [];
		foreach ($array as $key => $item)
		{
			if (!in_array($item, $unique, true))
			{
				$unique[$key] = $item;
			}
		}
		return $unique;
	}
	
	public static function empty(?array $array) : bool
	{
		if ($array === null)
		{
			return true;
		}
		$result = true;
		foreach ($array as $element)
		{
			if ( ($element !== null) &&
				 ($element !== '') )
			{
				$result = false;
			}
		}
		return $result;
	}
	
}
