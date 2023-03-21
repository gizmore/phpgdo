<?php
namespace GDO\Admin;

use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

/**
 * The admin section tab bar.
 *
 * @author gizmore
 */
final class GDT_AdminBar extends GDT_Bar
{

	protected function __construct()
	{
		parent::__construct();
		$this->addFields(
			GDT_Link::make()->text('btn_clearcache')->href(
				href('Core', 'ClearCache')),
			GDT_Link::make()->text('btn_modules')->href(
				href('Admin', 'Modules')),
			GDT_Link::make()->text('btn_admin_dashboard')->href(
				href('Admin', 'Dashboard')),
			GDT_Link::make()->text('users')->href(
				href('Admin', 'Users')),
			GDT_Link::make()->text('permissions')->href(
				href('Admin', 'Permissions')));
	}

}
