<?php
declare(strict_types=1);
namespace GDO\Net;

use GDO\Core\GDT_Method;
use GDO\Core\GDT_String;

/**
 * Packed IP datatype for lots of IP data.
 *
 * @version 7.0.3
 * @since 4.0.0
 * @author gizmore
 */
final class GDT_PackedIP extends GDT_String
{

	protected function __construct()
	{
		parent::__construct();
		$this->binary()->min(1)->max(16);
		$this->icon = 'url';
	}

	public function defaultLabel(): self { return $this->label('ip'); }

	##############
	### String ###
	##############

	public function isSearchable(): bool { return false; }

	############
	### Pack ###
	############

	public function inputToVar(array|string|null|GDT_Method $input): string
	{
		return self::ip2packed($input);
	}

	public static function ip2packed(string $ip): string
	{
		return (string)inet_pton($ip);
	}

	public function displayVar(string $var = null): string
	{
		return self::packed2ip($var);
	}

	public static function packed2ip(string $packed): string
	{
		return inet_ntop($packed);
	}

	###############
	### Current ###
	###############

	public function plugVars(): array
	{
		$n = $this->getName();
		return [
			[$n => self::ip2packed('12.13.14.15')],
			[$n => self::ip2packed('23.45.67.89')],
		];
	}

	############
	### Test ###
	############

	public function useCurrent(bool $useCurrent = true): GDT_PackedIP
	{
		$initial = $useCurrent ? GDT_IP::$CURRENT : null;
		return $this->initial($initial);
	}

}
