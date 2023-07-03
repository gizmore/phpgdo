<?php

namespace GDO\Core;

use Throwable;

class GDO_NoSuchCommandError extends GDO_Exception
{

    public function __construct(string $command, Throwable $previous = null)
    {
        parent::__construct('err_no_such_command', [$command], GDO_Exception::GDT_ERROR_CODE, $previous);
    }

}
