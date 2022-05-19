<?php
namespace GDO\PHPInfo\Method;

use GDO\Core\GDT;
use GDO\Core\Method;

final class PHPInfo extends Method
{
    public function getPermission() : ?string { return 'staff'; }
    
    public function execute() : GDT
	{
		phpinfo();
		die();
	}
}
