<?php
namespace GDO\Admin;

use GDO\UI\GDT_Button;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;

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
	public function getConfigModule() : ?GDO_Module
	{
		return isset($this->gdo) ? $this->gdo : null;
	}
	
	public function renderCell() : string
	{
		return $this->renderHTML();
	}
	
	public function renderHTML() : string
	{
		if ($module = $this->getConfigModule())
		{
		    if ($href = $module->href_administrate_module())
			{
			    $this->href($href);
				return parent::renderHTML();
			}
		}
		return GDT::EMPTY_STRING;
	}
	
}
