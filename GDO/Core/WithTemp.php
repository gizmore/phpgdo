<?php
namespace GDO\Core;

/**
 * Add temp variables to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
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
	public function tempHas(string $key) : bool
	{
		return isset($this->temp[$key]);
	}
	
	/**
	 * Get a temp var.
	 */
	public function tempGet(string $key, $default=null)
	{
		return isset($this->temp[$key]) ? $this->temp[$key] : $default;
	}
	
	/**
	 * Set a temp var.
	 */
	public function tempSet(string $key, $value) : self
	{
		if (!isset($this->temp))
		{
			$this->temp = [];
		}
		$this->temp[$key] = $value;
		return $this;
	}
	
	/**
	 * Remove a temp var.
	 */
	public function tempUnset(string $key) : self
	{
		unset($this->temp[$key]);
		return $this;
	}
	
	/**
	 * Remove all temp vars.
	 */
	public function tempReset() : self
	{
		unset($this->temp);
		return $this;
	}
	
}
