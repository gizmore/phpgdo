<?php
namespace GDO\Admin;

use GDO\UI\GDT_Button;
use GDO\Core\GDO_Module;

/**
 * Admin section button. Only visible if module has href_admin.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.1
 * @see GDT_Button
 * @see Module_Admin
 */
class GDT_ModuleAdminButton extends GDT_Button
{
	public function getModule() : GDO_Module
	{
		return $this->gdo;
	}
	
	public function renderHTML() : string
	{
	    if ($href = $this->getModule()->href_administrate_module())
		{
		    $this->href($href);
			return parent::renderCell();
		}
		return '';
	}
	
	public function renderJSON()
	{
	    if ($href = $this->getModule()->href_administrate_module())
	    {
	        $this->href($href);
    	    return sprintf('<a href="%s">%s</a>', $href, $this->renderLabel());
	    }
	}
	
}
