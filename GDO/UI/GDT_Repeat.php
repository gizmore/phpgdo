<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Method;
use GDO\Core\WithInput;

/**
 * A parameter repeater. @TODO: Move to Core module
 * 
 * Used for CLI parameter lists, like $sum 1,2,3,...
 * These need to be notNull and may not have an initial value.
 * This means it is always a positional finisher.
 * It is probably not possible at the moment to proxy a GDT_Composite like GDT_Message or GDT_Position.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 * @see WithProxy
 */
final class GDT_Repeat extends GDT
{
	use WithInput;
	use WithProxy;
	
	public function proxy(GDT $proxy) : self
	{
		$this->proxy = $proxy;
		$proxy->notNull();
		$proxy->initialValue(null);
		return $this;
	}
	
	public function htmlName() : string
	{
		$name = $this->getName();
		return " name=\"{$name}[]\"";
	}

	public function getVar()
	{
		$vars = [];
		$p = $this->proxy;
		foreach ($this->getRepeatInput() as $k => $input)
		{
			$var = $p->inputToVar($input);
			if ($input instanceof GDT_Method)
			{
				$this->inputs[$this->getName()][$k] = $var;
			}
			$vars[] = $var;
		}
		return $vars;
	}
	
	protected function getRepeatInput() : array
	{
		return $this->inputs[$this->getName()];
	}
	
	public function getValue()
	{
		$values = [];
		$p = $this->proxy;
		foreach ($this->getRepeatInput() as $input)
		{
			$var = $p->inputToVar($input);
			$value =  $p->toValue($var);
			$values[] = $value;
		}
		return $values;
	}
	
	public function plugVars() : array
	{
		$pv = $this->proxy->plugVars(); # sets of gdovar
		$back = [];
		foreach ($pv as $p) # $p is a gdovars with single string
		{
			foreach ($p as $name => $var)
			{
				# now make an array out of var
				$back[] = [$name => [$var, $var, $var]];
			}
		}
		return $back;
	}

}
