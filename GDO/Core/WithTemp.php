<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\Cache;

/**
 * Add temp variables to a GDO.
 *
 * @version 7.0.3
 * @since 7.0.0
 *
 * @author gizmore
 * @see GDT
 * @see GDO
 * @see GDO_User
 */
trait WithTemp
{

	public ?array $temp;

	/**
	 * Check if we have a temp var.
	 */
	public function tempHas(string $key): bool
	{
		return isset($this->temp[$key]);
	}

	/**
	 * Get a temp var.
	 */
	public function tempGet(string $key, $default = null)
	{
		Cache::$TEMP_READ++; #PP#delete#
		if (isset($this->temp[$key]))
		{
			Cache::$TEMP_CACHE++; #PP#delete#
			return $this->temp[$key];
		}
		return $default;
	}

	/**
	 * Set a temp var.
	 */
	public function tempSet(string $key, mixed $value): static
	{
		Cache::$TEMP_WRITE++; #PP#delete#
		if (!isset($this->temp))
		{
			$this->temp = [$key => $value];
		}
		else
		{
			$this->temp[$key] = $value;
		}
		return $this;
#		$this->temp = $this->temp ?: [];
		// A temp var does not make an IPC process utterly wrong... just outdated.
//		return $this->recache();
	}

	/**
	 * Remove a temp var.
	 * This means we need to recache other servers via IPC.
	 */
	public function tempUnset(string $key): static
	{
		if (isset($this->temp[$key]))
		{
			Cache::$TEMP_CLEAR++; #PP#delete#
			unset($this->temp[$key]);
			return $this->recache();
		}
		return $this;
	}

	/**
	 * Remove all temp vars.
	 */
	public function tempReset(): static
	{
		Cache::$TEMP_CLEAR_ALL++; #PP#delete#
		unset($this->temp);
		return $this->recache();
	}

}
