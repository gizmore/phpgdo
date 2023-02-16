<?php
namespace GDO\Core;

/**
 * Floating points return a float scalar as value.
 * 
 * @author gizmore
 */
class GDT_Float extends GDT_Int
{
	#######################
	### Input/Var/Value ###
	#######################
	public function toValue($var = null)
	{
		if ($var === null)
		{
			return null;
		}
		$var = trim($var);
		return $var === '' ? null : floatval($var);
	}

	public function htmlClass() : string
	{
		return sprintf(' gdt-float %s', parent::htmlClass());
	}

	public function _inputToVar($input)
	{
		if (parent::_inputToVar($input))
		{
		    return self::inputToVarS($input);
		}
	}
	
	/**
	 * Handle german and english inputs by keeping only the most right separator.
	 * More than one separator removes them all.
	 */
	public static function inputToVarS(string $input=null) : ?string
	{
		if ($input === null)
		{
			return null;
		}
		
		$input = trim($input);
		$pd = strrpos($input, '.'); # decimal
		$pk = strrpos($input, ','); # komma

		# 4 cases:
		if ( ($pd === false) && ($pk === false) )
		{
			# no separators
		}
		elseif ( ($pd === false) && ($pk !== false) )
		{
			# only kommas
			$c = substr_count($input, ',');
			if ($c === 1)
			{
				$input = str_replace(',', '.', $input);
			}
			else # mehr als ein komma
			{
				$input = str_replace(',', '', $input);
			}
		}
		elseif ( ($pd !== false) && ($pk === false) )
		{
			# only decimals
			$c = substr_count($input, '.');
			if ($c === 1)
			{
				# exactly 1 decimal point. ok!
			}
			else # mehr als ein decimal? Oo
			{
				$input = str_replace('.', '', $input);
			}
		}
		elseif ( ($pd !== false) && ($pk !== false) )
		{
			# both... keep the most right
			if ($pd > $pk)
			{
				$input = str_replace(',', '', $input);
			}
			else
			{
				$input = str_replace('.', '', $input);
				$input = str_replace(',', '.', $input);
			}
		}

		return $input;
	}
	
	public static function thousandSeperator() : string
	{
	    return t('thousands_seperator');
	}
	
	public static function decimalPoint() : string
	{
	    return t('decimal_point');
	}
	
	public static function displayS(string $var=null, int $decimals=4, string $dot=null, string $comma=null) : string
	{
		if ($var !== null)
		{
		    $dot = $dot !== null ? $dot : self::decimalPoint();
		    $comma = $comma != null ? $comma : self::thousandSeperator();
		    $display = number_format(floatval($var), $decimals, $dot, $comma);
		    return $display;
		}
		return self::none();
	}
	
	public int $decimals = 4;
	public function decimals(int $decimals) : self
	{
	    $this->decimals = $decimals;
	    return $this;
	}
	
	public function renderHTML() : string
	{
	    return self::displayS($this->var, $this->decimals);
	}
	
	public function gdoCompare(GDO $a, GDO $b) : int
	{
		$va = $a->gdoValue($this->name);
		$vb = $b->gdoValue($this->name);
		return ($va === $vb) ? 0 :
			(($va > $vb) ? 1 : - 1);
	}
	
}
