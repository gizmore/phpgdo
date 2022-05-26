<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Util\Arrays;

/**
 * A parameter repeater.
 * These need to be not null and may not have an initial value.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class GDT_Repeat extends GDT
{
	use WithProxy;
	
	public function proxy(GDT $proxy) : self
	{
		$this->proxy = $proxy;
		$proxy->notNull();
		$proxy->initialValue(null);
		return $this;
	}
	
// 	public function toValue($var)
// 	{
// 		$json_decode($var, true);
// 		return ["test"]
// 	}

	public function htmlFormName() : string
	{
		return $this->getName() . '[]';
	}
	
// 	public function getInputs() : array
// 	{
// 		return $this->proxy->geti
// 	}

	public function getInput(string $key=null) : string
	{
		
	}

	public function getValue()
	{
		
	}

}
