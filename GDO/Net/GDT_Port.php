<?php
namespace GDO\Net;

use GDO\Core\GDT_UInt;

final class GDT_Port extends GDT_UInt
{
	public $min = 1;
	public $max = 65535;
	public $bytes = 2;
}
