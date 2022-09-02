<?php
namespace GDO\UI;

use GDO\Core\GDT_Float;

/**
 * A length is a dimension for width and height.
 * Only the testing plug vars differ from an UInt.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class GDT_Length extends GDT_Float
{
	/**
	 * For tests we try two sizes.
	 */
	public function plugVars() : array
	{
		$n = $this->getName();
		return [
			[$n => 64],
			[$n => 128],
		];
	}

}
