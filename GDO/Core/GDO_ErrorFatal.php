<?php
namespace GDO\Core;

/**
 * Localized fatal runtime error.
 */
class GDO_ErrorFatal extends GDO_Exception
{

	public function __construct(string $key, array $args = null, $code = GDO_Exception::DEFAULT_ERROR_CODE)
	{
		parent::__construct(t($key, $args), $code);
	}

}
