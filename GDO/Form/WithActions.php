<?php
namespace GDO\Form;

use GDO\Core\GDT;

/**
 * Add an array of action GDT like buttons.
 * 
 * @author gizmore
 * @version 7.0.0
 */
trait WithActions
{
	/**
	 * @var GDT[]
	 */
	protected array $actions;
	
	public function actions() : array
	{
		if ($this->actions === null)
		{
			$this->actions = [];
		}
		return $this->actions;
	}
	
	public function hasActions() : bool
	{
		return !!$this->actions;
	}

}
