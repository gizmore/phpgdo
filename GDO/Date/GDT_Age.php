<?php
namespace GDO\Date;

final class GDT_Age extends GDT_Duration
{

	public $unsigned = true;

	public function gdtDefaultLabel(): ?string
    {
        return 'age';
    }

	public function renderHTML(): string { return Time::displayAgeTS($this->getValue()); }

}
