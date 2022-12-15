<?php
namespace GDO\Form;

use GDO\UI\WithTarget;
use GDO\Core\GDT;

/**
 * Add an html action attribute to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
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
		return isset($this->action) ? ' action="'.html($this->action).'"' : GDT::EMPTY_STRING;
	}
	
}
