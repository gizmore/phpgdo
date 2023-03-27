<?php
namespace GDO\Net;

use GDO\Core\GDT_String;

/**
 * Hostname datatype.
 * Optionally validate reachability.
 *
 * @version 6.10.1
 * @since 6.0.3
 * @author gizmore
 */
final class GDT_Hostname extends GDT_String
{

	###############
	### Resolve ###
	###############
	public int $min = 1;
	public int $max = 128;

	##################
	### GDT_String ###
	##################
	public bool $reachable = false;

	public function getIP() { return self::resolve($this->getVar()); }

	#################
	### Reachable ###
	#################

	public static function resolve($hostname) { return gethostbyname($hostname); }

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

	public function validateReachable($value)
	{
		return self::resolve($value) ? true : $this->error('err_unknown_host');
	}

}
