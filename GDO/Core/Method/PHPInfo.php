<?php
namespace GDO\Core\Method;

use GDO\Core\MethodAjax;
use GDO\Core\Application;

/**
 * Print phpinfo()
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
final class PHPInfo extends MethodAjax
{
	public function isTrivial() : bool { return false; }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function execute()
	{
		ob_start();
		phpinfo();
		$content = ob_get_contents();
		ob_end_clean();
		
		Application::instance()->timingHeader();
		
		echo $content;
		
		die();
	}

}
