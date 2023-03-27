<?php
declare(strict_types=1);
namespace GDO\Net;

use GDO\Core\GDT_String;

/**
 * Hostname datatype.
 * Optionally validate reachability.
 *
 * @version 7.0.3
 * @since 6.0.3
 * @author gizmore
 */
final class GDT_Hostname extends GDT_String
{

	###############
	### Resolve ###
	###############
	public ?int $min = 1;
	public ?int $max = 128;

	##################
	### GDT_String ###
	##################
	public bool $reachable = false;

	public function getIP(): string
	{
		return self::resolve($this->getVar());
	}

	#################
	### Reachable ###
	#################

	public static function resolve(string $hostname): string
	{
		return gethostbyname($hostname);
	}

	public function reachable(bool $reachable = true): self
	{
		$this->reachable = $reachable;
		return $this;
	}

	################
	### Validate ###
	################
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (parent::validate($value))
		{
			if (($value !== null) && ($this->reachable))
			{
				return $this->validateReachable($value);
			}
			return true;
		}
		return false;
	}

	public function validateReachable($value): bool
	{
		return self::resolve($value) || $this->error('err_unknown_host');
	}

}
