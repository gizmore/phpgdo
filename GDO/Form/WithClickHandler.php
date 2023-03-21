<?php
namespace GDO\Form;

/**
 * Add jQuery like click handling to a GDT.
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
trait WithClickHandler
{

	/**
	 * The click handler.
	 *
	 * @var callable
	 */
	public $onclick;

	public array $args;

	/**
	 * Set the click handler.
	 *
	 * @param callable $onclick
	 */
	public function onclick($onclick, ...$args): self
	{
		$this->onclick = $onclick;
		$this->args = $args;
		return $this;
	}

	/**
	 * Execute click handler.
	 */
	public function click(...$args)
	{
		if (isset($this->onclick))
		{
			$args = array_merge($this->args, $args);
			return call_user_func($this->onclick, ...$args);
		}
	}

}
