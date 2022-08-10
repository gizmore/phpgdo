<?php
namespace GDO\Core;

/**
 * A fixed decimal, database driven field.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 * 
 * @see GDT_Float
 * @see GDT_Money
 */
class GDT_Decimal extends GDT_Int
{
	##########
	### DB ###
	##########
	public function gdoColumnDefine() : string
	{
		$digits = sprintf("%d,%d", $this->digitsBefore + $this->digitsAfter, $this->digitsAfter);
		return "{$this->identifier()} DECIMAL($digits){$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	##############
	### Digits ###
	##############
	public int $digitsBefore = 9;
	public int $digitsAfter = 4;
	
	public function digitsBefore(int $before) : self
	{
		return $this->digits($before, $this->digitsAfter);
	}
	
	public function digitsAfter(int $after) : self
	{
		return $this->digits($this->digitsBefore, $after);
	}
	
	public function digits(int $before, int $after) : self
	{
		$this->digitsBefore = $before;
		$this->digitsAfter = $after;
		# compute step automatically nicely
		$step = $after < 1 ? 1 : floatval('0.'.str_repeat('0', $after-1).'1');
		return $after < 1 ? $this->step(1) : $this->step(sprintf("%.0{$after}f", $step));
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
	    return GDT_Float::displayS($this->getVar(), $this->digitsAfter);
	}
	
	#############
	### Value ###
	#############
	public function _inputToVar($input)
	{
		if ($input = parent::_inputToVar($input))
		{
			return GDT_Float::inputToVarS($input);
		}
	}
	
	public function plugVar() : string
	{
		return "3.14";
	}
	
	public function toVar($value) : ?string
	{
		$var = $value === null ? null : sprintf("%.0{$this->digitsAfter}f", $value);
		return $var;
	}
	
	public function toValue($var = null)
	{
		return $var === null ? null : round(floatval($var), $this->digitsAfter);
	}
	
}
