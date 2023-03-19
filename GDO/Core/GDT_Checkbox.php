<?php
namespace GDO\Core;

use GDO\UI\Color;
use GDO\Table\GDT_Filter;

/**
 * Boolean tri-state Checkbox; NULL, 1 and 0
 * Implemented as select to reflect undetermined status. Also HTML does not send unchecked boxes over HTTP.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class GDT_Checkbox extends GDT_Select
{
    const TRUE = '1';
    const FALSE = '0';
    const UNDETERMINED = '2';
    
    public function isSearchable() : bool { return false; }
    
    public function isDefaultAsc() : bool { return false; }
    
	protected function __construct()
	{
	    parent::__construct();
		$this->emptyVar = '2';
		$this->min = 0;
		$this->max = 1;
		$this->ascii(); # This enables string search (not binary).
		$this->caseS();
	}
	
	public function getChoices(): array
	{
		$choices = [
			'0' => t('enum_no'),
			'1' => t('enum_yes'),
		];
		if ($this->undetermined)
		{
			$this->emptyInitial(t('please_choose'), $this->emptyVar);
			$choices[$this->emptyVar] = $this->displayEmptyLabel();
		}
		return $choices;
	}
	
	####################
	### Undetermined ###
	####################
	public bool $undetermined = false;
	public function undetermined(bool $undetermined=true): static
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
	
	public function toValue($var = null)
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
	
	public function gdoExampleVars() : ?string
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
		return parent::htmlClass() . " gdt-cbx gdt-cbx-{$this->getVar()}";
	}
	
	public function renderHTML() : string
	{
	    return $this->displayVar($this->getVar());
	}
	
	public function renderForm() : string
	{
		$this->initChoices();
		$this->initThumbIcon();
		return parent::renderForm();
	}
	
	public function renderFilter(GDT_Filter $f) : string
	{
	    $vars = ['field' => $this, 'f'=> $f];
		return GDT_Template::php('Core', 'checkbox_filter.php', $vars);
	}
	
	####################
	### Dynamic Icon ###
	####################
	/**
	 * Init label icon with thumb up or thumb down.
	 */
	private function initThumbIcon(): static
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
		$name = $this->getName();
		return [
			[$name => '0'],
			[$name => '1'],
		];
	}
	
}
