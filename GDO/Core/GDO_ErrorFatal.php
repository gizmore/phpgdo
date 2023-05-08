<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Localized fatal runtime error.
 *
 * @version 7.0.3
 */
class GDO_ExceptionFatal extends GDO_Exception
{

	public function __construct(string $key, array $args = null, $code = GDO_Exception::GDO_ERROR_CODE, \Throwable $previous = null)
	{
		parent::__construct($key, $args, $code, $previous);
	}

}
