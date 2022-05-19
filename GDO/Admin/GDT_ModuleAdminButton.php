<?php
namespace GDO\Admin;

use GDO\UI\GDT_Button;
use GDO\Core\GDO;
use GDO\Core\GDO_Module;

class GDT_ModuleAdminButton extends GDT_Button
{
	private GDO_Module $module;

	public function gdo(GDO $gdo) : self
	{
		$this->module = $gdo;
	}

	public function getModule()
	{
		return $this->module;
	}

	
	
	public function renderCell() : string
	{
	    if ($href = $this->getModule()->href_administrate_module())
		{
		    $this->href($href);
			return parent::renderCell();
		}
	}
	
	public function renderJSON()
	{
	    if ($href = $this->getModule()->href_administrate_module())
	    {
	        $this->href($href);
    	    return sprintf('<a href="%s">%s</a>', $href, $this->displayLabel());
	    }
	}
	
}
