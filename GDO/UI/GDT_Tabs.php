<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\Core\WithFields;

/**
 * A navigation tab menu with tabs.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
final class GDT_Tabs extends GDT
{
	use WithFields;
	
	public function defaultName()
	{
		return 'tabs';
	}
	
	/**
	 * @var GDT_Tab[]
	 */
// 	private $tabs = [];
	public function getTabs() : array
	{
		return $this->getFields();
	}

	public function tab(GDT_Tab $tab)
	{
		return $this->addField($tab);
// 		$this->tabs[] = $tab;
// 		return $this;
	}
	
// 	public function getFields() : array
// 	{
// 		return $this->tabs;
// 	}
	
	##############
	### Render ###
	##############
	public function renderForm() : string
	{
		return GDT_Template::php('UI', 'cell/tabs.php', ['field' => $this, 'cell' => false]);
	}
	
	public function renderCell() : string
	{
		return GDT_Template::php('UI', 'cell/tabs.php', ['field' => $this, 'cell' => true]);
	}

}
