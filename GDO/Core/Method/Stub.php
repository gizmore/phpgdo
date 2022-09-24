<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\GDO_StubException;

/**
 * This method does nothing beside throwing an \GDO\Core\GDO_StubException and is the initial method value.
 * The real used method is stored in the global $me variable. I think it is the only global in gdo.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 * @see Method
 */
final class Stub extends Method
{
	public function getMethodTitle() : string
	{
		return 'Core::Stub';
	}
	
	public function execute()
	{
		throw new GDO_StubException('Core::Stub');
	}
	
}
