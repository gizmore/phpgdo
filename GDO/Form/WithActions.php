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
	
	/**
	 * 
	 * @return GDT[]
	 */
	public function actions() : array
	{
		if (!isset($this->actions))
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
