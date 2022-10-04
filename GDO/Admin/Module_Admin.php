<?php
namespace GDO\Admin;

use GDO\Core\GDO_Module;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Menu;

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
 * @version 7.0.1
 * @since 3.0.0
 */
class Module_Admin extends GDO_Module
{
	public int $priority = 14;
	
	##############
	### Module ###
	##############
	public function onLoadLanguage() : void
	{
		$this->loadLanguage('lang/admin');
	}
	
	public function getDependencies(): array
	{
		return ['Table'];
	}
	
	public function getFriendencies() : array
	{
		return ['Login', 'Register', 'Recovery'];
	}

	###############
	### Navbars ###
	###############
	public function onInitSidebar() : void
	{
		$menu = GDT_Menu::make('menu_admin');
	    GDT_Page::instance()->rightBar()->addField($menu);
		if (GDO_User::current()->isAdmin())
		{
		    $menu->addField(
		        GDT_Link::make('btn_admin')->href(
		            href('Admin', 'Modules')));
		}
	}
	
	public function onIncludeScripts() : void
	{
        $this->addCSS('css/admin.css');
	}
	
}
