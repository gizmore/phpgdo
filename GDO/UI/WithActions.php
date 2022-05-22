<?php
namespace GDO\UI;

/**
 * Adds an action menu to a GDT.
 * 
 * @author gizmore
 * @since 7.0.0
 * @version 6.10.1
 */
trait WithActions
{
	/**
	 * @var \GDO\UI\GDT_Bar
	 */
	public GDT_Menu $actions;

	public function actions() : GDT_Menu
	{
		if (!isset($this->actions))
		{
			$this->actions = GDT_Menu::make();
		}
		return $this->actions;
	}
	
	public function getActions() : GDT_Menu
	{
		return $this->actions;
	}

	public function hasActions() : bool
	{
		return isset($this->actions);
	}
	
}
