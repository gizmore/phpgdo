<?php
namespace GDO\Util;

/**
 * Random utility functions.
 * 
 * @author gizmore
 * @author noother
 * @author dloser
 * 
 * @version 7.0.2
 * @since 3.0.5
 */
final class Random
{
	const TOKEN_LEN = 16;
	const RAND_MAX = 4294967295;
	
	const NUMERIC = '0123456789';
	const ALPHAUP = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const ALPHALOW = 'abcdefghijklmnopqrstuvwxyz';
	const HEXLOWER = 'abcdef0123456789';
	const HEXUPPER = 'ABCDEF0123456789';
	const ALPHANUMLOW = 'abcdefghijklmnopqrstuvwxyz0123456789';
	const ALPHANUMUPLOW = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	const ALPHANUMUPLOWSPECIAL = '!"\'_.,%&/()=<>;:#+-*~@abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	
	/**
	 * Secure and evenly distributed random generator.
	 * @author dloser
	 * @author noother
	 * @author gizmore
	 */
	public static function rand(int $min=0, int $max=self::RAND_MAX): int
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
		return (int) ( $min + ($max-$min) * ($n/(self::RAND_MAX+1.0)) );
	}
	
	/**
	 * Generate a randomkey from a charset. A bit slow but should be secure random.
	 */
	public static function randomKey(int $len=self::TOKEN_LEN, string $alpha=self::ALPHANUMUPLOW): string
	{
		$alphalen = strlen($alpha) - 1;
		$key = '';
		for($i = 0; $i < $len; $i++)
		{
			$key .= $alpha[self::rand(0, $alphalen)];
		}
		return $key;
	}
	
	/**
	 * Pick a random array item securely.
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
	 * Get an insecure random number.
	 */
	public static function mrand(int $min=null, int $max=null): int
	{
	    return rand($min, $max);
	}
	
	/**
	 * Get an insecure random key.
	 */
	public static function mrandomKey(int $len=self::TOKEN_LEN, string $alpha=self::ALPHANUMUPLOW): string
	{
	    $alphalen = strlen($alpha) - 1;
	    $key = '';
	    for($i = 0; $i < $len; $i++)
	    {
	        $key .= $alpha[self::mrand(0, $alphalen)];
	    }
	    return $key;
	}

	/**
	 * Get an insecure random item from an array.
	 */
	public static function mrandomItem(array $array)
	{
		return count($array) ? $array[array_rand($array, 1)] : null;
	}
	
}
