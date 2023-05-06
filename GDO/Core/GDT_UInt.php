<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Unsigned version of GDT_Int
 * Sets a min value of 0.
 * Sets unsigned.
 * Sets default order direction to descending.
 * Base class of GDT_Object.
 *
 * @version 7.0.3
 * @since 6.5.0
 *
 * @author gizmore
 * @see GDT_Object
 */
class GDT_UInt extends GDT_Int
{

	public null|int|float $min = 0;

	public bool $unsigned = true;

	public function isDefaultAsc(): bool
	{
		return false;
	}

}
