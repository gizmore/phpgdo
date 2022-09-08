<?php
namespace GDO\Core;

/**
 * Not implemented yet.
 * @author gizmore
 */
class GDO_StubException extends GDO_Error
{
	public function __construct(string $what)
	{
		parent::__construct('err_method_is_stub', [$what]);
		
	}
	
}
