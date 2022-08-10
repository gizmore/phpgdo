<?php
namespace GDO\Date;

final class GDT_Age extends GDT_Duration
{
	public $unsigned = true;

	public function defaultLabel() : self { return $this->label('age'); }
	
	public function renderHTML() : string { return Time::displayAgeTS($this->getValue()); }
	
}
