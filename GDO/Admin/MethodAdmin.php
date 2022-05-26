<?php
namespace GDO\Admin;

use GDO\UI\GDT_Page;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

/**
 * Add the admin bar to page top.
 * Require admin permissions.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
trait MethodAdmin
{
	public function getPermission() : ?string { return 'admin'; }
	
	public function beforeExecute() : void
	{
		$this->renderAdminBar();
	}
	
	protected function renderAdminBar() : void
	{
		GDT_Page::instance()->topBar()->addField(GDT_AdminBar::make());
	}
	
	protected function renderPermissionBar() : void
	{
		GDT_Page::instance()->topBar()->addField(
			GDT_Bar::make()->addFields(
				GDT_Link::make()->href('Admin', 'Permissions')->label('permissions'),
				GDT_Link::make()->href('Admin', 'PermissionAdd')->label('permissions'),
				));
	}

}
