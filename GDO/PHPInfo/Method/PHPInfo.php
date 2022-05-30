<?php
namespace GDO\PHPInfo\Method;

use GDO\Core\Method;

final class PHPInfo extends Method
{
	public function isTrivial() { return false; }
	
    public function getPermission() : ?string { return 'staff'; }
    
    public function execute()
	{
		phpinfo();
		die();
	}
}
