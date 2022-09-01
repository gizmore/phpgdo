<?php
namespace GDO\Core;

/**
 * An exception with translated error message.
 * @TODO Move all GDO Exceptions to GDO/Core/Exception.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDO_Error extends GDO_Exception
{
	public function __construct(string $key, array $args = null, $code = self::DEFAULT_ERROR_CODE)
	{
		parent::__construct(t($key, $args), $code);
	}
	
}
