<?php
namespace GDO\Core;

class GDT_Percent extends GDT_Decimal
{

	protected function __construct()
	{
		parent::__construct();
		$this->digits(4, 1);
		$this->icon('percent');
	}

	public function displayVar(string $var = null): string
	{
		$back = $var === null ? '∞' : parent::displayVar($var);
		return $back . '%';
	}

}
