<?php
declare(strict_types=1);
namespace GDO\Core\Method;

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
final class Stub extends Method
{

	public function isTrivial(): bool
	{
		return false;
	}

	public function getMethodTitle(): string
	{
		return 'Core::Stub';
	}

	/**
	 * @throws GDO_StubException
	 */
	public function execute(): GDT
	{
		throw new GDO_StubException('Core::Stub');
	}

}
