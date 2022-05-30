<?php
namespace GDO\Admin;

use GDO\UI\GDT_Button;
use GDO\Core\GDO_Module;
use GDO\Core\WithGDO;

/**
 * Admin section button. Only visible if module has href_admin.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.1
 * @see GDT_Button
 * @see Module_Admin
 */
class GDT_ModuleAdminButton extends GDT_Button
{
	use WithGDO;
	
	public function getConfigModule() : ?GDO_Module
	{
		return @$this->gdo;
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
		return '';
	}
	
// 	public function renderJSON()
// 	{
// 	    if ($href = $this->getModule()->href_administrate_module())
// 	    {
// 	        $this->href($href);
//     	    return sprintf('<a href="%s">%s</a>', $href, $this->renderLabel());
// 	    }
// 	}
	
}
