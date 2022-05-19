<?php
namespace GDO\Net;

use GDO\Core\GDT_UInt;

/**
 * 16bit unsigned port.
 * @author gizmore
 * @version 7.0.0
 */
final class GDT_Port extends GDT_UInt
{
	public int $bytes = 2;
	
	public bool $unsigned = true;

	public ?int $min = 1;
	public ?int $max = 65535;

}
