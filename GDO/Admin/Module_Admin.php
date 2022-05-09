<?php
namespace GDO\Admin;

use GDO\Core\GDO_Module;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\UI\GDT_Page;

/**
 * The admin module offers:
 * 
 * - a user list and crud
 * - permission list and crud
 * - module administration
 * 
 * @TODO: restrict admin methods to a second admin password (like in gwf3)
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 3.0.0
 */
class Module_Admin extends GDO_Module
{
	##############
	### Module ###
	##############
	public function onLoadLanguage() : void { $this->loadLanguage('lang/admin'); }
// 	public function getDependencies() : array { return ['Login']; }

	###############
	### Navbars ###
	###############
	public function onInitSidebar() : void
	{
		if (GDO_User::current()->isAdmin())
		{
		    GDT_Page::$INSTANCE->rightNav->addField(
		        GDT_Link::make('btn_admin')->label('btn_admin')->href(
		            href('Admin', 'Modules')));
		}
	}
	
	public function onIncludeScripts() : void
	{
        $this->addCSS('css/admin.css');
	}
	
}
