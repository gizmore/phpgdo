<?php
namespace GDO\Net;

use GDO\Core\GDT_String;

/**
 * Packed IP datatype for lots of IP data.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 4.0.0
 */
final class GDT_PackedIP extends GDT_String
{
	public function defaultLabel(): static { return $this->label('ip'); }

	public function isSearchable() : bool { return false; }
	
	##############
	### String ###
	##############
	protected function __construct()
	{
		parent::__construct();
		$this->binary()->min(1)->max(16);
		$this->icon = 'url';
	}
	
	############
	### Pack ###
	############
	public static function ip2packed(string $ip) : string
	{
		return (string)inet_pton($ip);
	}
	
	public static function packed2ip(string $packed) : string
	{
		return inet_ntop($packed);
	}
	
	public function inputToVar($input): string
	{
		return self::ip2packed($input);
	}
	
	public function displayVar(string $var=null): string
	{
		return self::packed2ip($var);
	}
	
	###############
	### Current ###
	###############
	public function useCurrent(bool $useCurrent=true) : GDT_PackedIP
	{
		$initial = $useCurrent ? GDT_IP::$CURRENT : null;
        return $this->initial($initial);
	}

	############
	### Test ###
	############
	public function plugVars() : array
	{
		$n = $this->getName();
		return [
			[$n => self::ip2packed('12.13.14.15')],
			[$n => self::ip2packed('23.45.67.89')],
		];
	}
	
}
