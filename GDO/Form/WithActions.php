<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\Core\GDT_Fields;

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
	protected GDT_Fields $actions;
	
	/**
	 * 
	 * @return GDT[]
	 */
	public function actions() : GDT_Fields
	{
		if (!isset($this->actions))
		{
			$this->actions = GDT_Fields::make();
		}
		return $this->actions;
	}
	
	public function hasActions() : bool
	{
		return !!$this->actions;
	}

}
