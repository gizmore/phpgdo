<?php
declare(strict_types=1);
namespace GDO\Util;

use GDO\Core\GDT;

/**
 * Array utility.
 *
 * @version 7.0.3
 * @since 6.5.0
 * @author gizmore
 */
final class Arrays
{

	/**
	 * Turn a value into an array.
	 * Empty array on nulls.
	 */
	public static function arrayed($value): array
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
	public static function explode(string $string, string $delimiter = ','): array
	{
		return $string === '' ? [] : explode($delimiter, $string);
	}

	/**
	 * Recursive implode. Code taken from php.net.
	 * Original code by: kromped@yahoo.com
	 *
	 * @param string[] $pieces
	 */
	public static function implode(string $glue, array $pieces, array $retVal = []): string
	{
		foreach ($pieces as $r_pieces)
		{
			$retVal[] = is_array($r_pieces) ? '[' . self::implode($glue, $r_pieces) . ']' : $r_pieces;
		}
		return implode($glue, $retVal);
	}

	/**
	 * Recursive array_filter.
	 */
	public static function filter(array $array, callable $callback): array
	{
		$keep = [];
		foreach ($array as $k => $v)
		{
			if ($callback($v))
			{
				if (is_array($v))
				{
					$keep[$k] = self::filter($v, $callback);
				}
				else
				{
					$keep[$k] = $v;
				}
			}
		}
		return $keep;
	}

	/**
	 * Reverse an array but keep keys.
	 */
	public static function reverse(array $array): array
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
	public static function implodeHuman(array $array, string $conn = 'and'): string
	{
		switch (count($array))
		{
			case 0:
				return GDT::EMPTY_STRING;
			case 1:
				return array_pop($array);
			default:
				$last = array_pop($array);
				return implode(', ', $array) . ' ' . t($conn) . ' ' . $last;
		}
	}

	/**
	 * Unique array filter that respect same objects as equal.
	 */
	public static function unique(array $array): array
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

	/**
	 * Check if an array is empty. NULL values do not count.
	 */
	public static function empty(?array $array): bool
	{
		if ($array === null)
		{
			return true;
		}
		$result = true;
		foreach ($array as $element)
		{
			if (
				($element !== null) &&
				($element !== '')
			)
			{
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * Sum each field on all arrays.
	 */
	public static function sumEach(array $arrays): array
	{
		$result = [];
		foreach ($arrays as $array)
		{
			foreach ($array as $key => $value)
			{
				if (!isset($result[$key]))
				{
					$result[$key] = $value;
				}
				else
				{
					$result[$key] += $value;
				}
			}
		}
		return $result;
	}


	/**
	 * Remove a single element from an array.
	 */
	public static function remove(array &$array, mixed $object): array
	{
		foreach ($array as $key => $element)
		{
			if ($element === $object)
			{
				unset($array[$key]);
				break;
			}
		}
		return $array;
	}

	/**
	 * Return the same array but filter elelements by gdt names.
	 */
	public static function allExcept(array $array, string...$gdtNames): array
	{
		foreach ($array as $key => $gdt)
		{
			if (in_array($gdt->getName(), $gdtNames, true))
			{
				unset($array[$key]);
			}
		}
		return $array;
	}

	/**
	 * Sum all elements of an array via a callable.
	 */
	public static function sum(array $array, $callable): int
	{
		$back = 0;
		foreach ($array as $element)
		{
			$back += $callable($element);
		}
		return $back;
	}

    public static function last(array $a): mixed
    {
        return self::empty($a) ? null : $a[array_key_last($a)];
    }

}
