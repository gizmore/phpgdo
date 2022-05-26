<?php
namespace GDO\CLI\Method;

use GDO\Core\Method;
use GDO\Core\GDT_String;

/**
 * Reply the input back.
 * GDOv7 style =)
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
final class Ekko extends Method
{
	public function gdoParameters() : array
	{
		return [
			GDT_String::make('text')->notNull(),
		];
	}
	
	public function execute()
	{
		return $this->gdoParameter('text')->validated();
	}
	
}
