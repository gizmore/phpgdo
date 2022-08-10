<?php
namespace GDO\Admin;

use GDO\UI\GDT_Link;
use GDO\Core\WithGDO;
use GDO\Core\GDO_Module;

/**
 * Is searchable, filterable, orderarble because it's the modulename.
 * 
 * @author gizmore
 */
final class GDT_ModuleNameLink extends GDT_Link
{
	use WithGDO;
	
	public function isTestable() : bool { return false; }
	public function isOrderable() : bool { return true; }
	public function isSearchable() : bool { return true; }
	public function isFilterable() : bool { return true; }
	
	public function getModuleLinked() : GDO_Module
	{
		return $this->gdo;
	}
	
    public function renderHTML() : string
	{
		$this->labelRaw($this->getModuleLinked()->getName());
		$this->href(href('Admin', 'Configure', "&module=".$this->gdo->getName()));
		return parent::renderHTML();
	}
	
// 	public function renderJSON()
// 	{
// // 	    $this->href(href('Admin', 'Configure', "&module=".$this->gdo->getName()));
// 	    return sprintf('<a href="%s">%s</a>', $this->href, $this->gdo->getName());
// 	}
	
}
