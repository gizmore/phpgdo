<?php
namespace GDO\Core;

/**
 * Add temp variables to a GDT.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.0
 * 
 * @see GDT
 * @see GDO
 * @see GDO_User
 */
trait WithTemp
{
	# performance stats
	public static int $TEMP_READ = 0;
	public static int $TEMP_CACHE = 0;
	public static int $TEMP_WRITE = 0;
	public static int $TEMP_CLEAR = 0;
	public static int $TEMP_CLEAR_ALL = 0;
	
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
		self::$TEMP_READ++;
		if (isset($this->temp[$key]))
		{
			self::$TEMP_CACHE++;
			return $this->temp[$key];
		}
		return $default;
	}
	
	/**
	 * Set a temp var.
	 */
	public function tempSet(string $key, $value) : self
	{
		self::$TEMP_WRITE++;
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
		self::$TEMP_CLEAR++;
		unset($this->temp[$key]);
		return $this;
	}
	
	/**
	 * Remove all temp vars.
	 */
	public function tempReset() : self
	{
		self::$TEMP_CLEAR_ALL++;
		unset($this->temp);
		return $this;
	}
	
}
