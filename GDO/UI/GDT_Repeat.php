<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Method;

/**
 * A parameter repeater.
 * These need to be not null and may not have an initial value.
 * This means it is always a positional finisher.
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
	
	public function htmlFormName() : string
	{
		return $this->getName() . '[]';
	}

	public function getVar()
	{
		$vars = [];
		$p = $this->proxy;
		foreach ($this->input as $k => $input)
		{
			$var = $p->inputToVar($input);
			if ($input instanceof GDT_Method)
			{
				$this->input[$k] = $var;
			}
			$vars[] = $var;
		}
		return $vars;
	}
	
	public function getValue()
	{
		$values = [];
		$p = $this->proxy;
		foreach ($this->input as $input)
		{
			$var = $p->inputToVar($input);
			$value =  $p->toValue($var);
			$values[] = $value;
		}
		return $values;
	}

	public $input = [];
	public function input($input = null) : self
	{
		$this->input[] = $input;
		return $this;
	}
	
	
}
