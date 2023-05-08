<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\GDO_Exception;
use GDO\Core\GDO_StubException;
use GDO\Core\GDT;
use GDO\Core\Method;

/**
 * This method does nothing beside throwing an \GDO\Core\GDO_StubException and is the initial method value.
 * The real used method is stored in the global $me variable. I think it is the only global in gdo.
 *
 * @version 7.0.3
 * @since 7.0.1
 * @author gizmore
 * @see Method
 */
class Stub extends Method
{

	public function isHiddenMethod(): bool
	{
		return true;
	}

	public function isTrivial(): bool
	{
		return false;
	}

	public function getMethodTitle(): string
	{
		global $me;
		return $me->getMethodTitle();
	}

	public function execute(): GDT
	{
		global $me;
		throw new GDO_Exception('err_method_is_stub', [$me->gdoClassName()]);
	}

}
