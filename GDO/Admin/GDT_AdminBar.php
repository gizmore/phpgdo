<?php
namespace GDO\Admin;

use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

final class GDT_AdminBar extends GDT_Bar
{
	protected function __construct()
	{
		parent::__construct();
		$this->addFields(
			GDT_Link::make()->label('btn_clearcache')->href(
				href('Admin', 'ClearCache')),
			GDT_Link::make()->label('btn_modules')->href(
				href('Admin', 'Modules')));
	}

}
