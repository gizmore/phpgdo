<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\UI\WithTarget;

/**
 * Add an html action attribute to a GDT.
 *
 * @version 7.0.2
 * @since 7.0.0
 * @author gizmore
 */
trait WithAction
{

	use WithTarget;

	public string $action;

	public function action(string $action): self
	{
		$this->action = $action;
		return $this;
	}

	public function htmlAction(): string
	{
		return isset($this->action) ? ' action="' . html($this->action) . '"' : GDT::EMPTY_STRING;
	}

}
