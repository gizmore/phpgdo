<?php
namespace GDO\PHPInfo;

use GDO\Core\GDO_Module;

class Module_PHPInfo extends GDO_Module
{
	public function hrefAdministration() : ?string
	{
		return href('PHPInfo', 'PHPInfo');
	}
	
}
