<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\GDO_StubException;

/**
 * This method does nothing beside throwing an exception and is the initial method value.
 * The method is stored in the global $me.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class Stub extends Method
{
	public function execute()
	{
		throw new GDO_StubException('Core::Stub');
	}
	
}
