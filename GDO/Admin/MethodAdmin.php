<?php
namespace GDO\Admin;

use GDO\UI\GDT_Page;

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
	public function getPermission() { return 'admin'; }
	
	public function beforeExecute()
	{
		GDT_Page::instance()->topBar()->addField(GDT_AdminBar::make());
	}

}
