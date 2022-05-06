<?php
namespace GDO\CLI\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\GDT_String;

final class Concat extends Method
{
	public function gdoParameters()
	{
		return [
			GDT_String::make('one')
		];
	}
	
	public function execute(): GDT
	{
	}


}

