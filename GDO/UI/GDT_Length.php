<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_Float;

/**
 * A length is a dimension for width and height.
 * Only the testing plug vars differ from an UInt.
 *
 * @version 7.0.3
 * @since 7.0.1
 * @author gizmore
 */
final class GDT_Length extends GDT_Float
{

	/**
	 * For tests we try two sizes.
	 */
	public function plugVars(): array
	{
		$n = $this->getName();
		return [
			[$n => '512'], # kinda large image?
		];
	}

}
