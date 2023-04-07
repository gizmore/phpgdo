<?php

namespace GDO\Core;

class GDO_NoSuchCommandError extends GDO_Error
{

	public function __construct(string $command, int $code = GDO_Exception::DEFAULT_ERROR_CODE)
	{
		parent::__construct('err_unknown_command', [html($command)], $code);
	}

}
