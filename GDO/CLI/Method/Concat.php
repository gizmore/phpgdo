<?php
namespace GDO\CLI\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\GDT_String;

final class Concat extends Method
{
	public function gdoParameters() : array
	{
		return [
			GDT_String::make('one'),
			GDT_String::make('two'),
		];
	}
	
	public function execute(): GDT
	{
	}


}

