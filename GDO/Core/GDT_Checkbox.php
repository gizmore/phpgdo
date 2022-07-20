<?php
namespace GDO\Core;

use GDO\UI\Color;

/**
 * Boolean tri-state Checkbox; NULL, 1 and 0
 * Implemented as select to reflect undetermined status. Also HTML does not send unchecked boxes over HTTP.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_Checkbox extends GDT_Select
{
    const TRUE = '1';
    const FALSE = '0';
    const UNDETERMINED = '2';
    
    public function isSearchable() : bool { return false; }
    
    public function isOrderDefaultAsc() : bool { return false; }
    
	protected function __construct()
	{
	    parent::__construct();
		$this->emptyVar = '2';
		$this->min = 0;
		$this->max = 1;
		$this->ascii(); # This enables string search (not binary).
		$this->caseS();
	}
	
	public function initChoices()
	{
		if (!$this->choices)
		{
			$this->choices([
				'0' => t('enum_no'),
				'1' => t('enum_yes'),
			]);
			if ($this->undetermined)
			{
				$this->emptyInitial(t('please_choose'), $this->emptyVar);
				$this->choices[$this->emptyVar] = $this->displayEmptyLabel();
			}
		}
		return $this;
	}
	
	################
	### Database ###
	################
	/**
	 * Get TINYINT(1) column define.
	 */
	public function gdoColumnDefine() : string
	{
		return "{$this->identifier()} TINYINT(1) UNSIGNED ".
		  "{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	####################
	### Undetermined ###
	####################
	public bool $undetermined = false;
	public function undetermined(bool $undetermined=true) : self
	{
	    $this->max = $undetermined ? 2 : 1;
		$this->undetermined = $undetermined;
		return $this;
	}
	
	###################
	### Var / Value ###
	###################
	public function toVar($value) : ?string
	{
		if ($value === true) { return '1'; }
		elseif ($value === false) { return '0'; }
		else { return '2'; }
	}
	
	public function toValue(string $var = null)
	{
		if ($var === '0') { return false; }
		elseif ($var === '1') { return true; }
		else { return null; }
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		$this->initChoices();
		if ($value === true)
		{
		    return true;
		}
		if ($value === false)
		{
		    return true;
		}
		if ($value === null)
		{
		    return parent::validate($value);
		}
		return $this->errorInvalidChoice();
	}
	
	protected function errorInvalidVar($var)
	{
	    return t('err_invalid_gdt_var', [$this->gdoHumanName(), html($var)]);
	}
	
	public function gdoExampleVars()
	{
	    return '0|1';
	}
	
	##############
	### Render ###
	##############
	public function displayVar(string $var=null) : string
	{
	    if ($var === null)
	    {
	        return t('enum_undetermined_yes_no');
	    }
	    switch ($var)
	    {
	        case '0': return Color::red(t('enum_no'));
	        case '1': return Color::green(t('enum_yes'));
	        case '2': return t('enum_undetermined_yes_no');
	        default: return $this->errorInvalidVar($var);
	    }
	}
	
	public function displayChoice($choice) : string
	{
		return $this->displayVar($choice);
	}
	
	public function htmlClass() : string
	{
		return parent::htmlClass() . " gdt-checkbox-{$this->getVar()}";
	}
	
	public function renderCell() : string
	{
	    return $this->displayVar($this->getVar());
	}
	
	public function renderForm() : string
	{
		$this->initChoices();
		$this->initThumbIcon();
		return parent::renderForm();
	}
	
// 	public function renderCell() : string
// 	{
// 		return $this->displayVar($this->getVar());
// 	}
	
// 	public function renderJSON()
// 	{
// 	    return $this->displayValue($this->getVar());
// 	}
	
	public function renderFilter($f) : string
	{
	    $vars = ['field' => $this, 'f'=> $f];
		return GDT_Template::php('Core', 'checkbox_filter.php', $vars);
	}
	
// 	public function htmlChoiceVar($choice) : string
// 	{
// 		return $this->toVar($choice);
// 	}

	####################
	### Dynamic Icon ###
	####################
	/**
	 * Init label icon with thumb up or thumb down.
	 */
	private function initThumbIcon() : self
	{
	    switch ($this->getVar())
	    {
	        case '0': return $this->icon('thumbs_down');
	        case '1': return $this->icon('thumbs_up');
	        default: return $this->icon('thumbs_none');
	    }
	}

	public function plugVars() : array
	{
		return [
			'0',
			'1',
		];
	}
	
}
