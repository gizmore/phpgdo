<?php
namespace GDO\Core;

/**
 * Add a name to a GDT.
 * Display human classname.
 * Add trait WithModule.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 6.0.0
 * @see WithModule
 */
trait WithName
{
	use WithModule;
	
	public string $name;
	
	public function hasName() : bool
	{
		return isset($this->name);
	}
	
	public function getName() : ?string
	{
		return isset($this->name) ? $this->name : null;
	}
	
	public function getDefaultName() : ?string
	{
		return null;
	}
	
	public function name(string $name = null) : self
	{
		if ($name)
		{
			$this->name = $name;
		}
		else
		{
			unset($this->name);
		}
		return $this;
	}
	
	###############
	### Factory ###
	###############
	public static function make(string $name = null) : self
	{
		return self::makeNamed($name);
	}
	
	public static function makeNamed(string $name = null) : self
	{
		$obj = new static();
		$name = $name === null ? $obj->getDefaultName() : $name;
		$obj->name($name);
		return $obj;
	}

}
