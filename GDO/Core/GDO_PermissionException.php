<?php
namespace GDO\Core;

use GDO\User\GDO_Permission;

/**
 * Thrown when a user has not the right permissions.
 *
 * @version 7.0.0
 * @author gizmore
 */
class GDO_PermissionException extends GDO_Error
{

	public Method $method;
	public GDO_Permission $permission;

	public function __construct(Method $method, GDO_Permission $permission)
	{
		parent::_construct('err_no_permission', [$method->getName(), $permission->renderName()]);
		$this->method = $method;
		$this->permission = $permission;
	}

}
