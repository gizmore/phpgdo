<?php
namespace GDO\Install\Method;

use GDO\Core\GDT;
use GDO\Core\Method;

/**
 * Installer welcome page.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.1
 */
class Welcome extends Method
{
	public function execute() : GDT
	{
		return $this->templatePHP('page/welcome.php');
	}
	
}
