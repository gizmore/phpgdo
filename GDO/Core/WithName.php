<?php
namespace GDO\Core;

use GDO\Language\Trans;

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
	
	public ?string $name = null;
	
	public function hasName() : bool
	{
		return isset($this->name);
	}
	
	public function getName() : ?string
	{
		return $this->name;
	}
	
	public function getDefaultName() : ?string
	{
		return null;
	}
	
	public function name(string $name = null) : self
	{
		$this->name = $name;
		return $this;
	}
	
	public static function make(string $name = null) : self
	{
		$obj = new static();
		$name = $name ? $name : $obj->getDefaultName();
		$obj->name($name);
		return $obj;
	}

	public function gdoHumanName() : string
	{
		$shortname = self::gdoShortNameS();
		$key = strtolower($shortname);
		if (Trans::hasKey($key))
		{
			return t($key);
		}
		$key = strtolower($this->getName());
		if (Trans::hasKey($key))
		{
			return t($key);
		}
		return $shortname;
	}
}
