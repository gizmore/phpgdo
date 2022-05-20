<?php
namespace GDO\Form;

use GDO\Core\GDT_Fields;
use GDO\UI\GDT_Menu;

/**
 * Add an array of action GDT like buttons.
 * 
 * @author gizmore
 * @version 7.0.0
 */
trait WithActions
{
	private GDT_Menu $actions;
	
	/**
	 * @return GDT_Menu
	 */
	public function actions() : GDT_Fields
	{
		if (!isset($this->actions))
		{
			$this->actions = GDT_Menu::make();
		}
		return $this->actions;
	}
	
	public function hasActions() : bool
	{
		return isset($this->actions);
	}

}
