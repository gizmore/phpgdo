<?php
namespace GDO\Form;

/**
 * Add jquery like click handling to a GDT.
 * 
 * @author gizmore
 * @since 7.0.0
 */
trait WithClickHandler
{
	public $onclick = null;
	
	public function onclick(?callable $onclick) : self
	{
		$this->onclick = $onclick;
		return $this;
	}
	
	public function click(...$args)
	{
		if ($this->onclick)
		{
			return call_user_func($this->onclick, ...$args);
		}
	}
	
}
