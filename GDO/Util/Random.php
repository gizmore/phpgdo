<?php
declare(strict_types=1);
namespace GDO\Util;

/**
 * Random utility functions.
 *
 * @version 7.0.3
 * @since 3.0.5
 * @author dloser
 *
 * @author gizmore
 * @author noother
 */
final class Random
{

	final public const TOKEN_LEN = 16;
	final public const RAND_MAX = 4294967295;

	final public const NUMERIC = '0123456789';
	final public const ALPHAUP = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	final public const ALPHALOW = 'abcdefghijklmnopqrstuvwxyz';
	final public const HEXLOWER = 'abcdef0123456789';
	final public const HEXUPPER = 'ABCDEF0123456789';
	final public const ALPHANUMLOW = 'abcdefghijklmnopqrstuvwxyz0123456789';
	final public const ALPHANUMUPLOW = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	final public const ALPHANUMUPLOWSPECIAL = '!"\'_.,%&/()=<>;:#+-*~@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	/**
	 * Generate a randomkey from a charset. A bit slow but should be secure random.
	 */
	public static function randomKey(int $len = self::TOKEN_LEN, string $alpha = self::ALPHANUMUPLOW): string
	{
		$alphalen = strlen($alpha) - 1;
		$key = '';
		for ($i = 0; $i < $len; $i++)
		{
			$key .= $alpha[self::rand(0, $alphalen)];
		}
		return $key;
	}

	/**
	 * Secure and evenly distributed random generator.
	 *
	 * @author dloser
	 * @author noother
	 * @author gizmore
	 */
	public static function rand(int $min = 0, int $max = self::RAND_MAX): int
	{
		# Generate random numbers
		static $BUFFER = '';
		if (strlen($BUFFER) < 4)
		{
			$BUFFER = openssl_random_pseudo_bytes(1024);
		}

		# Take 4 bytes and unpack to a signed int
		$n = unpack('L', substr($BUFFER, 0, 4));
		# thx to dloser we convert to unsigned on 32 bit arch
		$n = $n[1] + 2147483648 * (PHP_INT_SIZE === 4);

		# Eat from random buffer
		$BUFFER = substr($BUFFER, 4);

		# Evenly distributed
		return (int)($min + ($max - $min) * ($n / (self::RAND_MAX + 1.0)));
	}

	/**
	 * Pick a random array item securely.
	 *
	 * @param array $array
	 */
	public static function randomItem(array $array)
	{
		# Implement me :)
		# @TODO Create a new utility: FantasyNameGenerator. Use syllables and implement Random::randomItem() like Random::MrandomItem().
	}

	################
	### Insecure ### but faster
	################
	public static function srand(int $seed): void
	{
		srand($seed);
	}

	/**
	 * Get an insecure random key.
	 */
	public static function mrandomKey(int $len = self::TOKEN_LEN, string $alpha = self::ALPHANUMUPLOW): string
	{
		$alphalen = strlen($alpha) - 1;
		$key = '';
		for ($i = 0; $i < $len; $i++)
		{
			$key .= $alpha[self::mrand(0, $alphalen)];
		}
		return $key;
	}

	/**
	 * Get an insecure random number.
	 */
	public static function mrand(int $min = 0, int $max = self::RAND_MAX): int
	{
		return rand($min, $max);
	}

	/**
	 * Get an insecure random item from an array.
	 */
	public static function mrandomItem(array $array)
	{
		return count($array) ? $array[array_rand($array, 1)] : null;
	}

	/**
	 * @param mixed[] $array
	 * @param float[] $chances The chances as 0.0 - 1.0
	 * @return mixed the random array element.
	 */
	public static function mrandomItemBiased(array $array, array $chances)
	{
		if (!count($array))
		{
			return null;
		}
		$sum = 0.0;
		foreach ($chances as $flt)
		{
			$sum += $flt;
		}
		$i = 0;
		$j = 0;
		$r = self::mrand();
		while ($i < $r)
		{
			$i += round(self::RAND_MAX / ($sum / $chances[$j++]));
		}
		return array_values($array)[$j-1];
	}

	public static function mrandomItemCallback(array $array, callable $chanceCallback)
	{
		$chances = array_map($chanceCallback, array_values($array));
		return self::mrandomItemBiased($array, $chances);
	}

	/**
	 * return true or false based on a probability between 0.00 and 1.00
	 */
	public static function mchance(float $chance): bool
	{
		return self::mrand() > (self::RAND_MAX / $chance);
	}

}
