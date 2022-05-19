<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\WithGDO;

/**
 * Arbitrary method call on a gdo for cell display.
 * 
 * @deprecated Ugly idea
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.1
 */
final class GDT_Cell extends GDT
{
	use WithGDO;
	use WithLabel;
	
	public $method = null;
	public $methodArgs = null;
	public function method($method=null, $args=null)
	{
		$this->method = $method;
		$this->methodArgs = $args;
		return $this;
	}
	
	public function callMethod()
	{
		return call_user_func([$this->gdo, $this->method]);
	}
	
	public function render() : string
	{
		return $this->callMethod();
	}

	public function renderCell() : string
	{
		return $this->callMethod();
	}
	
	public function renderHeader() : string
	{
		return GDT_String::make()->label($this->label, $this->labelArgs)->renderHeader();
	}
	
	public function renderJSON()
	{
		return [
			$this->name => $this->callMethod(),
		];
	}

}
