<?php
namespace GDO\Form;

/**
 * Add jQuery like click handling to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
trait WithClickHandler
{
	/**
	 * The click handler.
	 * @var callable
	 */
	public $onclick;
	
	/**
	 * Set the click handler.
	 * @param callable $onclick
	 */
	public function onclick($onclick) : self
	{
		$this->onclick = $onclick;
		return $this;
	}
	
	/**
	 * Execute click handler.
	 */
	public function click(...$args)
	{
		if (isset($this->onclick))
		{
			return call_user_func($this->onclick, ...$args);
		}
	}
	
}
