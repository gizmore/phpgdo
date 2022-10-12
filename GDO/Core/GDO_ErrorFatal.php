<?php
namespace GDO\Core;

class GDO_ErrorFatal extends GDO_Exception
{
	public function __construct(string $key, array $args = null, $code = GDO_ErrorFatal::DEFAULT_ERROR_CODE)
	{
		parent::__construct(t($key, $args), $code);
	}
	
}
