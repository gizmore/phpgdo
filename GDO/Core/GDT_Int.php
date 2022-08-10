<?php
namespace GDO\Core;

use GDO\DB\Query;

/** 
 * Database capable base integer class.
 * Is also is the base class for GDT_Objects
 * 
 * Control ->bytes(4) for size.
 * Control ->unsigned([true]) for unsigned.
 * Control ->min() and ->max() for validation.
 * Control ->step() for html5 fancy.
 * 
 * Is inherited by GDT_Object for auto_inc relation.
 * Can validate uniqueness.
 * Can compare gdo instances.
 * Is searchable and orderable.
 * Uses WithLabel, WithFormFields, WithDatabase and WithOrder.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see GDT_UInt
 * @see GDT_Decimal
 * @see GDT_Float
 * @see GDT_Object
 */
class GDT_Int extends GDT_DBField
{
	public string $icon = 'numeric';
	
	public function toValue($var = null)
	{
		return $var === null ? null : intval($var, 10);
	}
	
	#############
	### Bytes ###
	#############
	public int $bytes = 4;
	public function bytes(int $bytes) : self
	{
		$this->bytes = $bytes;
		return $this;
	}
	
	############
	### Step ###
	############
	public float $step = 1.0;
	public function step(float $step) : self
	{
		$this->step = $step;
		return $this;
	}
	
	################
	### Unsigned ###
	################
	public bool $unsigned = false;
	public function unsigned(bool $unsigned = true) : self
	{
		$this->unsigned = $unsigned;
		return $this;
	}
	
	###############
	### Min/Max ###
	###############
	public ?float $min = null;
	public ?float $max = null;
	public function min(float $min) : self { $this->min = $min; return $this; }
	public function max(float $max) : self { $this->max = $max; return $this; }
	
	################
	### Validate ###
	################
// 	public function is_numeric(string $input) : bool
// 	{
// 		return !!Regex::firstMatch('/^([0-9][-+\\d\\.,]*)$/iD', $input);
// 	}
	
	public function validate($value) : bool
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
// 				if (!$this->is_numeric($this->getRequestVar()))
// 				{
// 					return $this->numericError();
// 				}
				
				if ( (($this->min !== null) && ($value < $this->min)) ||
					 (($this->max !== null) && ($value > $this->max)) )
				{
					return $this->intError();
				}
				if (!$this->validateUnique($value))
				{
					return $this->error('err_db_unique');
				}
			}
			return true;
		}
		return false;
	}
	
	protected function validateUnique($value) : bool
	{
		if ($this->unique)
		{
			$condition = "{$this->identifier()}=".GDO::quoteS($value);
			if ($this->gdo->isPersisted()) // persisted
			{ // ignore own row
				$condition .= " AND NOT ( " . $this->gdo->getPKWhere() . " )";
			}
			return $this->gdo->table()->select('1')->where($condition)->first()->exec()->fetchValue() !== '1';
		}
		return true;
	}
	
	private function numericError() : bool
	{
		return $this->error('err_input_not_numeric');
	}
	
	/**
	 * Appropiate min / max validation.
	 * @return boolean
	 */
	private function intError() : bool
	{
		if (($this->min !== null) && ($this->max !== null))
		{
			return $this->error('err_int_not_between', [$this->min, $this->max]);
		}
		if ($this->min !== null)
		{
			return $this->error('err_int_too_small', [$this->min]);
		}
		if ($this->max !== null)
		{
			return $this->error('err_int_too_large', [$this->max]);
		}
	}
	
	/**
	 * 4 === 4
	 */
	public function plugVar() : string
	{
	    return "4"; # don't get lost
	}
	
	public function gdoExampleVars() : ?string
	{
	    if ( ($this->min !== null) && ($this->max !== null) )
	    {
	        if ($this->min === $this->max)
	        {
	            return $this->min;
	        }
	        else
	        {
	            return $this->min . '-' . $this->max;
	        }
	    }
	    if ($this->max !== null)
	    {
	        return '-∞-' . $this->max;
	    }
	    if ($this->min !== null)
	    {
	        return $this->min . '-∞';
	    }
	    return t('number');
	}
	
	
	##########
	### DB ###
	##########
	public function gdoColumnDefine() : string
	{
		$unsigned = $this->unsigned ? " UNSIGNED" : "";
		return "{$this->identifier()} {$this->gdoSizeDefine()}INT{$unsigned}{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	protected function gdoSizeDefine()
	{
		switch ($this->bytes)
		{
			case 1: return "TINY";
			case 2: return "MEDIUM";
			case 4: return "";
			case 8: return "BIG";
			default: throw new GDO_Error('err_int_bytes_length', [$this->bytes]);
		}
	}
	
	##############
	### Render ###
	##############
	public function htmlClass() : string
	{
	    return sprintf(' gdt-num %s', parent::htmlClass());
	}
	
	public function renderForm() : string
	{
		return GDT_Template::php('Core', 'integer_form.php', ['field'=>$this]);
	}
	
	public function renderHTML() : string
	{
		return GDT_Float::displayS($this->getVar(), 0);
	}
	
	##############
	### Filter ###
	##############
	public function renderFilter($f) : string
	{
		return GDT_Template::php('Core', 'integer_filter.php', ['field' => $this, 'f' => $f]);
	}
	
	public function filterVar(string $key=null)
	{
		return [];
	}
	
	public function filterQuery(Query $query, $rq=null) : self
	{
	    if ($filter = $this->filterVar($rq))
	    {
	        if ($condition = $this->searchQuery($query, $filter, true))
	        {
	            $this->filterQueryCondition($query, $condition);
	        }
	    }
	    return $this;
	}
	
	public function filterGDO(GDO $gdo, $filtervalue) : bool
	{
		return true; # @TODO implement GDT_Int::filterGDO
// 		$min = $filtervalue['min'];
// 		$max = $filtervalue['max'];
// 		$var = $this->getVar();
// 		if ( ($min !== null) && ($var < $min) )
// 		{
// 			return false;
// 		}
// 		if ( ($max !== null) && ($var > $max) )
// 		{
// 			return false;
// 		}
// 		return true;
	}
	
	/**
	 * Comparing two integers is not that hard.
	 */
	public function gdoCompare(GDO $a, GDO $b) : int
	{
		return
			$a->gdoValue($this->name) -
			$b->gdoValue($this->name);
	}
	
	##############
	### Search ###
	##############
	public function searchQuery(Query $query, $searchTerm, $first)
	{
	    return $this->searchCondition($searchTerm);
	}
	
// 	public function searchGDO($searchTerm)
// 	{
// 	    $haystack = (string) $this->getVar();
// 	    return strpos($haystack, $searchTerm) !== false;
// 	}
	
	public function searchCondition($searchTerm) : string
	{
		$searchTerm = (int)$searchTerm;
		return "{$this->name} = {$searchTerm}";
	}
	
}
