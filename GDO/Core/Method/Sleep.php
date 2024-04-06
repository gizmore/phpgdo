<?php
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Float;
use GDO\Core\GDT_Response;
use GDO\Core\Method;

final class Sleep extends Method
{

    public function isCLI(): bool
    {
        return true;
    }

    public function getCLITrigger(): string
    {
        return 'sleep';
    }

    public function gdoParameters(): array
    {
        return [
            GDT_Float::make('seconds')->min(0)->notNull(),
        ];
    }

    public function getSeconds(): float
    {
        return $this->gdoParameterValue('seconds');
    }

    public function getMicros(): int
    {
        return round($this->getSeconds()/1000000.0);
    }

    public function execute(): GDT
    {
        usleep($this->getMicros());
        return GDT_Response::make();
    }

}
