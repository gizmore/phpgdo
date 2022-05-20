<?php
namespace GDO\Core;

/**
 * Add temp variables to a GDO.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 * @see GDO
 * @see GDT
 * @see GDO_User
 */
trait WithTemp
{
	public array $temp;
	
	public function tempReset() : self
	{
		unset($this->temp);
		return $this;
	}
	
	public function tempGet(string $key)
	{
		return isset($this->temp) ? @$this->temp[$key] : null;
	}
	
	public function tempSet(string $key, $value) : self
	{
		if (!isset($this->temp))
		{
			$this->temp = [];
		}
		$this->temp[$key] = $value;
		return $this;
	}
	
	public function tempUnset($key) : self
	{
		unset($this->temp[$key]);
		return $this;
	}
	
	public function tempHas($key) : bool
	{
		return !!$this->tempGet($key);
	}
	
}
