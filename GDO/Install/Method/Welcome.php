<?php
namespace GDO\Install\Method;
use GDO\Core\Method;
class Welcome extends Method
{
	public function execute() : GDT
	{
		return $this->templatePHP('page/welcome.php');
	}
}
