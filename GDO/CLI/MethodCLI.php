<?php
namespace GDO\CLI;

use GDO\Form\MethodForm;

/**
 * Abstract CLI method does not work via HTTP.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
abstract class MethodCLI extends MethodForm
{
	public function isCLI() : bool { return true; }
	
}
