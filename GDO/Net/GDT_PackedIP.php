<?php
namespace GDO\Net;

use GDO\Core\GDT_Char;

/**
 * Packed IP datatype for lots of IP data.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 4.0.0
 */
final class GDT_PackedIP extends GDT_Char
{
	public function defaultLabel() : self { return $this->label('ip'); }

	public function isSearchable() : bool { return false; }
	
	protected function __construct()
	{
		parent::__construct();
		$this->icon = 'url';
		$this->binary()->length(16);
	}
	
	public static function ip2packed(string $ip) : string
	{
		return inet_pton($ip);
	}
	
	public static function packed2ip(string $packed) : string
	{
		return unpack("XXXX:XXXX:XXXX:XXXX:XXXX:XXXX:XXXX:XXXX, $packed);
	}
	
	### Current ###
	###############
	public function useCurrent(bool $useCurrent=true) : GDT_PackedIP
	{
		$initial = $useCurrent ? GDT_PackedIP::$CURRENT : null;
        return $this->initial($initial);
	}

	##############
	### String ###
	##############
	public int $min = 3;
	public int $max = 45;
	public int $encoding = GDT_PackedIP::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^[.:0-9a-f]{3,45}$/D";
	public string $icon = 'url';
	
	public function defaultLabel() : GDT_PackedIP { return $this->label('ip'); }

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
GDT_PackedIP::$CURRENT = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
