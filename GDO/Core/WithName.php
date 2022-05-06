<?php
namespace GDO\Core;

/**
 * Add a name to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
trait WithName
{
	public string $name;
	
	public function getDefaultName() : string
	{
		# default null
	}
	
	public function name(string $name) : self
	{
		$this->name = $name;
		return $this;
	}
	
	public static function make($name = null)
	{
		$obj = new static();
		$obj->name($name ? $name : $obj->getDefaultName());
		return $obj;
	}

}
