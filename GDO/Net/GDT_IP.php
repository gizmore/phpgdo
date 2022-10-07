<?php
namespace GDO\Net;

use GDO\Core\GDT_String;

/**
 * IP datatype.
 * Current IP is assigned at the very bottom.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 4.0.0
 */
final class GDT_IP extends GDT_String
{
	public function isSearchable() : bool { return false; }
	
	###############
	### IP Util ###
	###############
	public static string $CURRENT = '::1'; # for connections like websocket too!
	public static function current() : string { return self::$CURRENT; }
	
	public static function isIPv4(string $ip): bool
	{
		return strpos($ip, '.') !== false;
	}
	
	public static function isIPv6(string $ip): bool
	{
		return strpos($ip, ':') !== false;
	}
	
	/**
	 * Get the IP netmask for a number of bits.
	 * @example netmask(8) => 11111111 00000000 00000000 00000000 => 
	 * @param int $bits
	 * @return int
	 */
	public static function netmask($bits)
	{
	    return bindec(str_repeat('1', $bits) . str_repeat('0', 32 - $bits));
	}
	
	public static function isLocal(string $ip=null) : bool
	{
		$ip = $ip ? $ip : self::$CURRENT;
		return
		  ($ip === '::1') ||
		  (str_starts_with($ip, '127')) ||
		  (str_starts_with($ip, '192.168')) ||
		  (str_starts_with($ip, '169.254')) ||
		  (str_starts_with($ip, '10.')) ||
		  ((ip2long($ip) & self::netmask(12)) === bindec('10101100000100000000000000000000'));
	}
	
	###############
	### Current ###
	###############
	public function useCurrent(bool $useCurrent=true) : self
	{
		$initial = $useCurrent ? self::$CURRENT : null;
        return $this->initial($initial);
	}

	##############
	### String ###
	##############
	public int $min = 3;
	public int $max = 45;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^[.:0-9a-f]{3,45}$/D";
	public string $icon = 'url';
	
	public function defaultLabel() : self { return $this->label('ip'); }

	############
	### Test ###
	############
	public function plugVars() : array
	{
		$n = $this->getName();
		return [
			[$n => '12.13.14.15'],
			[$n => '23.45.67.89'],
		];
	}
	
}

# Assign current IP.
GDT_IP::$CURRENT = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
