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
		if ($module = $this->getConfigModule())
		{
		    if ($href = $module->hrefAdministration())
			{
			    $this->href($href);
				return parent::renderCell();
			}
		}
		return GDT::EMPTY_STRING;
	}
	
}
