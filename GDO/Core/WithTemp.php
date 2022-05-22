<?php
namespace GDO\Core;

/**
 * Add temp variables to a GDO.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 * @see GDO
 * @see GDT
 * @see GDO_User
 */
trait WithTemp
{
	public array $temp;
	
	/**
	 * Reset all temp vars.
	 */
	public function tempReset() : self
	{
		unset($this->temp);
		return $this;
	}
	
	/**
	 * Get a temp var.
	 * @param mixed $default
	 * @return string
	 */
	public function tempGet(string $key, $default=null)
	{
		return isset($this->temp) ? @$this->temp[$key] : $default;
	}
	
	/**
	 * @param mixed $value
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
	
	public function tempUnset(string $key) : self
	{
		unset($this->temp[$key]);
		return $this;
	}
	
	public function tempHas($key) : bool
	{
		return !!$this->tempGet($key);
	}
	
}
