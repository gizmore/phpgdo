<?php
namespace GDO\Net;

use GDO\Core\GDT_Object;

final class GDT_SubDomain extends GDT_Object
{
	protected function __construct()
	{
		parent::__construct();
		$this->table(GDO_SubDomain::table());
	}
	
}
