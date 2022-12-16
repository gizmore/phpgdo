<?php
namespace GDO\Admin;

use GDO\UI\GDT_Page;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

/**
 * Add the admin bar to page top.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 5.0.0
 * @see \GDO\Core\Method
 */
trait MethodAdmin
{
	public function isShownInSitemap(): bool
	{
		return false;
	}
	
	public function getPermission(): ?string
	{
		return 'staff';
	}
	
	public function onRenderTabs(): void
	{
		$this->renderAdminBar();
	}
	
	protected function renderAdminBar(): void
	{
		GDT_Page::instance()->topResponse()->addField(GDT_AdminBar::make());
	}
	
	protected function renderPermissionBar(): void
	{
		GDT_Page::instance()->topResponse()->addField(
			GDT_Bar::make()->addFields(
				GDT_Link::make()->href(href('Admin', 'Permissions'))->text('permissions'),
				GDT_Link::make()->href(href('Admin', 'PermissionAdd'))->text('add_permissions'),
				GDT_Link::make()->href(href('Admin', 'PermissionGrant'))->text('link_grant_perm')));
	}

}
