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
	
	public function getRepeatInput() : array
	{
		$name = $this->getName();
		return isset($this->inputs[$name]) ? $this->arrayed((array)$this->inputs[$name]) : GDT::EMPTY_ARRAY;
	}
	
	private function arrayed(array $array): array
	{
		$back = array_reverse($array);
		foreach($back as $i => $el)
		{
			if ($el === '')
			{
				unset($back[$i]);
			}
			else
			{
				break;
			}
		}
		return array_reverse($back);
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
	
	##############
	### Repeat ###
	##############
	public int $minRepeat = 1;
	public function minRepeat(int $minRepeat): self
	{
		$this->minRepeat = $minRepeat;
		return $this;
	}
	
	public int $maxRepeat = 10;
	public function maxRepeat(int $maxRepeat): self
	{
		$this->maxRepeat = $maxRepeat;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderLabel(): string
	{
		return $this->proxy->renderLabel();
	}
	
	public function renderForm(): string
	{
		$html = '';
		$in = $this->getRepeatInput();
		$i = count($in);
		if ($i < $this->maxRepeat)
		{
			$in[] = '';
		}
		for ($i = count($in); $i < $this->minRepeat; $i++)
		{
			$in[] = '';
		}
		foreach ($in as $i => $var)
		{
			$gdt = $this->getRepeatProxyElement($i);
			if ($i > 0)
			{
				$gdt->notNull(false);
			}
			$html2 = $gdt->var($var)->renderForm();
			$html .= $html2;
		}
		return $html;
	}
	
	private function getRepeatProxyElement(int $i): GDT
	{
		$newName = "{$this->getName()}[{$i}]";
		return $this->proxy->gdtCopy($newName);
	}
	
	
	public function validate($value): bool
	{
		$p = $this->proxy;

		if (empty($value))
		{
			return $p->validate(null);
		}
		
		$in = $this->getRepeatInput();
		foreach ($in as $input)
		{
			$var = $p->inputToVar($input);
			$val = $p->toValue($var);
			if (!$p->validate($val))
			{
				return false;
			}
		}
		return true;
	}
	
	public function renderError(): string
	{
		return $this->proxy->renderError();
	}

}
