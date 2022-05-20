<?php
namespace GDO\Admin;

use GDO\UI\GDT_Page;

/**
 * Add the admin bar to page top.
 * Require admin permissions.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
trait MethodAdmin
{
	public function getPermission() : ?string { return 'admin'; }
	
	public function beforeExecute() : void
	{
		GDT_Page::instance()->topBar()->addField(GDT_AdminBar::make());
	}

}
