<?php
namespace GDO\Core;

/**
 * Add a name to a GDT.
 * Display human classname.
 * Add trait WithModule.
 * 
 * @see WithModule
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
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
	
	##############
	### Render ###
	##############
	public function htmlName() : string
	{
		if ($name = $this->getName())
		{
			return sprintf(' name="%s"', $name);
		}
		return '';
	}
	
	###############
	### Factory ###
	###############
	public static function make(string $name = null) : self
	{
		return self::makeNamed($name)->defaultLabel();
	}
	
	public static function makeNamed(string $name = null) : self
	{
		$obj = new static();
		$name = $name ? $name : $obj->getDefaultName();
		$obj->name($name);
		return $obj;
	}

}
