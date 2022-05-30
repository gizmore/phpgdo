<?php
namespace GDO\Form;

use GDO\UI\GDT_Menu;

/**
 * Add an array of actions GDT like buttons.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
trait WithActions
{
	private GDT_Menu $actions;
	
	public function actions() : GDT_Menu
	{
		if (!isset($this->actions))
		{
			$this->actions = GDT_Menu::make();
		}
		return $this->actions;
	}
	
	public function getActions()
	{
		return isset($this->actions) ? $this->actions : null;
	}

	public function hasActions() : bool
	{
		return isset($this->actions) && $this->actions->hasFields();
	}

}
