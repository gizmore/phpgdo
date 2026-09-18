<?php
namespace GDO\Net;

use GDO\Core\GDT_Object;

final class GDT_Domain extends GDT_Object
{

	protected function __construct()
	{
		parent::__construct(); #PP#delete#
		$this->table(GDO_Domain::table());
	}

}
