<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\User\GDO_Permission;

/**
 * Thrown when a user has not the right permissions.
 *
 * @version 7.0.3
 * @author gizmore
 */
class GDO_PermissionException extends GDO_Exception
{

	public Method $method;
	public string $reason;

	public function __construct(Method $method, string $reason, int $code=GDO_Exception::PERM_ERROR_CODE)
	{
		parent::__construct('err_no_permission', [$method->getMethodTitle(), $reason], $code);
		$this->method = $method;
		$this->reason = $reason;
	}

}
