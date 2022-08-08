<?php
namespace GDO\PHPInfo\Method;

use GDO\Core\Method;

final class PHPInfo extends Method
{
	public function isTrivial() : bool { return false; }
	
    public function getPermission() : ?string { return 'staff'; }
    
    public function execute()
	{
		phpinfo();
		die();
	}
}
