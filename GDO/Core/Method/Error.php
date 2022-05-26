<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\GDT_String;
use GDO\Core\GDT;
use GDO\UI\GDT_Error;

/**
 * Render an arbitrary error. 
 * @author gizmore
 * @version 7.0.0
 */
final class Error extends MethodPage
{
	public function isTrivial() : bool { return false; } # Auto-Test's for 200 code, so not trivial to test.
	
	public function gdoParameters() : array
	{
		return [
			GDT_String::make('error')->notNull(),
		];
	}
	
	public function execute() : GDT
	{
		return GDT_Error::make()->textEscaped()->textRaw($this->gdoParameterValue('error'));
	}
	
}
