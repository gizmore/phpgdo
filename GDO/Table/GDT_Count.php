<?php
namespace GDO\Table;

use GDO\Core\GDT_UInt;

/**
 * Simple row number counter++
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.3.0
 */
class GDT_Count extends GDT_UInt
{
	private $num = 1;

	public function isVirtual() : bool { return true; }
	
	public function isOrderable() : bool { return false; }
	
	public function defaultLabel(): static { return $this; }

	public function render() : string
	{
		return $this->num++;
	}

}
