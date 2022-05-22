<?php
namespace GDO\Admin;

use GDO\UI\GDT_Link;

/**
 * Is searchable, filterable, orderarble because it's the modulename.
 * 
 * @author gizmore
 */
final class GDT_ModuleNameLink extends GDT_Link
{
	public function isOrderable() : bool { return true; }
	public function isSearchable() : bool { return true; }
	public function isFilterable() : bool { return true; }
	
    public function renderCell() : string
	{
		$this->label($this->gdo->getName());
		$this->href(href('Admin', 'Configure', "&module=".$this->gdo->getName()));
		return parent::renderCell();
	}
	
	public function renderJSON()
	{
	    $this->href(href('Admin', 'Configure', "&module=".$this->gdo->getName()));
	    return sprintf('<a href="%s">%s</a>', $this->href, $this->gdo->getName());
	}
	
}
