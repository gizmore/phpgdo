<?php
namespace GDO\CLI\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\GDT_String;

/**
 * Glue strings together.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Concat extends Method
{
	public function gdoParameters() : array
	{
		return [
			GDT_String::make('a')->notNull(),
			GDT_String::make('b')->notNull(),
			GDT_String::make('glue')->initial(''),
		];
	}
	
	public function execute(): GDT
	{
		$a = $this->gdoParameterVar('a');
		$b = $this->gdoParameterVar('b');
		$glue = $this->gdoParameterVar('glue');
		return $a . $glue . $b;
	}


}

