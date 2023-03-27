<?php
namespace GDO\Util;

/**
 * Random utility functions.
 *
 * @version 7.0.2
 * @since 3.0.5
 * @author dloser
 *
 * @author gizmore
 * @author noother
 */
final class Random
{

	public const TOKEN_LEN = 16;
	public const RAND_MAX = 4294967295;

	public const NUMERIC = '0123456789';
	public const ALPHAUP = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	public const ALPHALOW = 'abcdefghijklmnopqrstuvwxyz';
	public const HEXLOWER = 'abcdef0123456789';
	public const HEXUPPER = 'ABCDEF0123456789';
	public const ALPHANUMLOW = 'abcdefghijklmnopqrstuvwxyz0123456789';
	public const ALPHANUMUPLOW = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	public const ALPHANUMUPLOWSPECIAL = '!"\'_.,%&/()=<>;:#+-*~@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

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
	public static function mrand(int $min = null, int $max = null): int
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
	 * return true or false based on a probability between 0.00 and 1.00
	 */
	public static function mchance(float $chance): bool
	{
		return self::mrand() > (self::RAND_MAX / $chance);
	}

}
