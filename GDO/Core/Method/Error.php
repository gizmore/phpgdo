<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\GDT_String;
use GDO\Core\GDT;

/**
 * Render an arbitrary error. 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
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
		$error = $this->gdoParameterVar('error');
		return $this->error('error', [html($error)]);
	}
	
}
