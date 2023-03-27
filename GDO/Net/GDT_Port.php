<?php
declare(strict_types=1);
namespace GDO\Net;

use GDO\Core\GDT_UInt;

/**
 * 16bit unsigned port.
 *
 * @version 7.0.3
 * @author gizmore
 */
final class GDT_Port extends GDT_UInt
{

	public int $bytes = 2;

	public bool $unsigned = true;

	public int|null|float $min = 1;
	public int|null|float $max = 65535;

}
