<?php
namespace GDO\Form;

use GDO\UI\WithTarget;

/**
 * Add an html action attribute to a GDT.
 * @author gizmore
 * @since 7.0.0
 */
trait WithAction
{
	use WithTarget;
	
	public string $action;
	public function action(string $action) : self
	{
		$this->action = $action;
		return $this;
	}
	
	public function htmlAction() : string
	{
		if (isset($this->action))
		{
			return sprintf(' action="%s"', html($this->action));
		}
		return '';
	}
	
}
