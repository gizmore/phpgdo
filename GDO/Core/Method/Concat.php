<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\GDT_String;

/**
 * Concatenate two strings.
 * 
 * @author gizmore
 * @since 7.0.0
 */
final class Concat extends Method
{
	/**
	 * Method paramteres.
	 */
	public function gdoParameters() : array
	{
		return [
			GDT_String::make('a')->required(),
			GDT_String::make('b')->required(),
			GDT_String::make('glue')->initial(''),
		];
	}
	
	public function execute()
	{
		$a = $this->gdoParameterVar('a');
		$glue = $this->gdoParameterVar('glue');
		$b = $this->gdoParameterVar('b');
		return $a . $glue . $b;
	}

}
