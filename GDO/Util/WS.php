<?php
declare(strict_types=1);
namespace GDO\Util;

use GDO\Core\Application;

/**
 * Websocket i/o utility
 *
 * @version 7.0.3
 */
final class WS
{

	public static function wrFloat(?float $float): string
	{
        if ($float === null)
        {
            $float = NAN;
        }
		return pack('f', floatval($float));
	}


	public static function wrDouble(?float $double): string
	{
        if ($double === null)
        {
            $double = NAN;
        }
		return pack('d', doubleval($double));
	}


	public static function wr8(int $value): string
	{
		return self::wrN(1, $value);
	}


	public static function wr16(int $value): string
	{
		return self::wrN(2, $value);
	}


	public static function wr32(int $value): string
	{
		return self::wrN(4, $value);
	}


	public static function wr64(int $value): string
	{
		return self::wrN(8, $value);
	}


	public static function wrString(string $string): string
	{
		return urlencode($string) . "\0";
	}


	public static function wrN(int $bytes, int $value): string
	{
		$value = (int)$value;
		$write = '';
		for ($i = 0; $i < $bytes; $i++)
		{
			$write = chr($value & 0xFF) . $write;
			$value >>= 8;
		}
		return $write;
	}


}
