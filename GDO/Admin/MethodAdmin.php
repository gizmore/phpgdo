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
 * @version 7.0.1
 * @since 5.0.0
 * @see \GDO\Core\Method
 */
trait MethodAdmin
{
	public function isShownInSitemap() : bool
	{
		return false;
	}
	
	public function getPermission() : ?string
	{
		return 'admin';
	}
	
	public function onRenderTabs() : void
	{
		$this->renderAdminBar();
	}
	
	protected function renderAdminBar() : void
	{
		GDT_Page::instance()->topResponse()->addField(GDT_AdminBar::make());
	}
	
	protected function renderPermissionBar() : void
	{
		GDT_Page::instance()->topResponse()->addField(
			GDT_Bar::make()->addFields(
				GDT_Link::make()->href(href('Admin', 'Permissions'))->text('permissions'),
				GDT_Link::make()->href(href('Admin', 'PermissionAdd'))->text('add_permissions'),
				GDT_Link::make()->href(href('Admin', 'PermissionGrant'))->text('link_grant_perm')));
	}
	
// 	public function getMethodTitle() : string
// 	{
// 		$key = strtolower(sprintf('mt_%s_%s', $this->getModuleName(), $this->getMethodName()));
// 		return t($key);
// 	}

}
