<?php
namespace GDO\Net\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Net\GDT_IP;

final class MyIP extends Method
{

    public function execute(): GDT
    {
        return GDT_IP::make('your_ip')->useCurrent();
    }

}