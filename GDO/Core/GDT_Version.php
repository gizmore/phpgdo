<?php
namespace GDO\Core;

final class GDT_Version extends GDT_String
{
	public $min = 5;
	public $max = 15;
	public $pattern = "/\\d+\\.\\d+\\.\\d+/iD";
	
	public function var(string $var)
	{
		parent::var($var);
	}
	
	public function toValue(string $var)
	{
		return new Version($var);
	}
	
}
