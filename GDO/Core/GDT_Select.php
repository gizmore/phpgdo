<?php
namespace GDO\Core;

use GDO\UI\TextStyle;
use GDO\Table\GDT_Filter;

/**
 * An HTML select.
 * Can autocomplete input, like `./gdo.sh mail.send giz <.....>`.
 * Validates min/max selected.
 * Fill choices in getChoices()
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 6.0.0
 */
class GDT_Select extends GDT_ComboBox
{
	const SELECTED = ' selected="selected"'; # for options
	
	###################
	### Var / Value ###
	###################
	public function getSelectedVar() : ?string
	{
		$var = $this->getVar();
		return $var === null ? $this->emptyVar : $var;
	}
	
	public function getVar()
	{
		if (null === ($var = parent::getVar()))
		{
			return $this->multiple ? '[]' : null;
		}
		elseif ($this->multiple)
		{
			return is_array($var) ? json_encode($var) : $var; # NO visible json, no pretty print.
		}
		else
		{
			return $var;
		}
	}
	
	public function getValue()
	{
// 		if ($this->valueConverted)
// 		{
// 			return $this->value;
// 		}
		$var = $this->getVar();
		if ($var === null)
		{
			$value = $this->multiple ? GDT::EMPTY_ARRAY : null;
		}
		else
		{
			$value = $this->toValue($var);
		}
// 		$this->valueConverted = true;
// 		$this->value = $value;
		return $value;
	}
	
	public function inputToVar($input) : ?string
	{
		if ($input === $this->emptyVar)
		{
			$input = null;
		}
		return parent::inputToVar($input);
	}

	public function toVar($value) : ?string
	{
		if ($value === null)
		{
			return null;
		}
		
		# Multiple var
		if ($this->multiple)
		{
	        return json_encode(array_values($value));
		}
		
		# Single var
		if ($value === $this->emptyVar) 
		{
			return null;
		}
		if (false !== ($var = array_search($value, $this->initChoices(), true)))
		{
			return $var;
		}

		return null;
	}
	
	public function toValue($var = null)
	{
		return $this->selectToValue($var);
	}
	
	public function selectToValue(string $var = null)
	{
		if ($var === null)
		{
			return null;
		}
		if ($this->multiple)
		{
			return json_decode($var, true);
		}
		if ($var === $this->emptyVar)
		{
			return $this->emptyVar;
		}
		$this->initChoices();
		if (isset($this->choices[$var]))
		{
			return $this->choices[$var];
		}
		else
		{
			$value = $this->toClosestChoiceValue($var);
			return $value;
		}
	}
	
	public function getChoices(): array
	{
		return GDT::EMPTY_ARRAY;
	}
	
	public function initChoices() : array
	{
		if (!isset($this->choices))
		{
			$this->choices($this->getChoices());
		}
		return $this->choices;
	}
	
	protected function toClosestChoiceValue($var)
	{
	    $candidatesZero = [];
	    $candidatesMiddle = [];
	    $this->initChoices();
	    foreach ($this->choices as $vaar => $value)
	    {
	    	$name = is_string($value) ? $value : $value->getName();
	    	
	    	if (strcasecmp($var, $name) === 0)
	    	{
	    		return $value;
	    	}
	    	
	        $pos = stripos($vaar, $var);
	        if ($pos === false)
	        {
        		$pos = stripos($name, $var);
        		if ($pos === false)
        		{
        			continue;
	        	}
	        }
	        if ($pos === 0)
	        {
	            $candidatesZero[] = $value;
	            $candidatesMiddle[] = $value;
	        }
	        else
	        {
	            $candidatesMiddle[] = $value;
	        }
	    }
	    
	    if (count($candidatesZero) === 1)
	    {
	        return $candidatesZero[0];
	    }
	    
	    if (count($candidatesMiddle) === 1)
	    {
	        return $candidatesMiddle[0];
	    }
	    
	    if (count($candidatesMiddle) > 1)
	    {
        	$candidatesMiddle = array_slice($candidatesMiddle, 0, 5);
        	foreach ($candidatesMiddle as $i => $candidate)
        	{
        		if (is_object($candidate))
        		{
        			$candidatesMiddle[$i] = $candidate->renderName();
        		}
        	}
        	$this->error('err_select_candidates', [implode('|', $candidatesMiddle)]);
	    }
	}
	
	public function getGDOData() : array
	{
		$var = $this->getVar();
		return ( ($var === null) || ($var === $this->emptyVar) ) ?
			GDT::EMPTY_ARRAY : [$this->name => $var];
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		return $this->multiple ?
			$this->validateMultiple($value) :
			$this->validateSingle($value);
	}
	
	private function validateMultiple($values)
	{
		if ($values === null)
		{
			return $this->validateNull($values);
		}
		
	    if ($values)
	    {
    		foreach ($values as $value)
    		{
    			if (!$this->validateSingle($value))
    			{
    				return false;
    			}
    		}
	    }
		
		if ( (isset($this->minSelected)) && (count($values) < $this->minSelected) )
		{
			return $this->error('err_select_min', [$this->maxSelected]);
		}
		
		if ( (isset($this->maxSelected)) && (count($values) > $this->maxSelected) )
		{
			return $this->error('err_select_max', [$this->maxSelected]);
		}
		
		return true;
	}
	
	protected function validateSingle($value)
	{
		if ( ($value === null) || ($value === $this->emptyVar) )
		{
		    if ($this->getVar() && ($value !== $this->emptyVar))
		    {
		        return $this->errorInvalidChoice();
		    }
			return $this->notNull ? $this->errorNull() : true;
		}
		
		$this->initChoices();
		
		if ($value instanceof GDO)
		{
    		if (isset($this->choices[$value->getID()]))
    		{
    		    return true;
    		}
		}
		
		if ($value instanceof GDT)
		{
			if (isset($this->choices[$value->getVar()]))
			{
				return true;
			}
		}
		
		if (in_array($value, $this->choices, true)) # check single identity
		{
		    return true;
		}
		
		if (!$this->multiple)
		{
    		if (array_key_exists($this->toVar($value), $this->choices))
    		{
    		    return true;
    		}
		}
 		
 		return $this->errorInvalidChoice();
	}
	
	protected function errorInvalidChoice()
	{
		return $this->error('err_invalid_choice');
	}
	
	#############
	### Empty ###
	#############
	public string $emptyVar = '0';
	public function emptyVar(string $emptyVar) : self
	{
		$this->emptyVar = $emptyVar;
		return $this;
	}
	
	public string $emptyLabelRaw;
	public string $emptyLabelKey;
	public ?array $emptyLabelArgs;
	public function emptyLabel(string $key, $args=null) : self
	{
		unset($this->emptyLabelRaw);
		$this->emptyLabelKey = $key;
		$this->emptyLabelArgs = $args;
		return $this;
	}
	
	public function emptyLabelRaw(string $text) : self
	{
		$this->emptyLabelRaw = $text;
		unset($this->emptyLabelKey);
		unset($this->emptyLabelArgs);
		return $this;
	}

	public function hasEmptyLabel() : bool
	{
		return isset($this->emptyLabelRaw) || isset($this->emptyLabelKey);
	}
	
	public function emptyInitial(string $labelKey, string $emptyVar='0')
	{
		return $this->emptyLabel($labelKey)->initial($emptyVar);
	}
	
	public function displayEmptyLabel() : string
	{
		if (isset($this->emptyLabelRaw))
		{
			return $this->emptyLabelRaw;
		}
		if (isset($this->emptyLabelKey))
		{
			return t($this->emptyLabelKey, $this->emptyLabelArgs);
		}
		return GDT::EMPTY_STRING;
	}

	################
	### Multiple ###
	################
	public bool $multiple = false;
	public function multiple(bool $multiple=true) : self
	{
		$this->multiple = $multiple;
		return $this->initial($this->initial);
	}
	
	public int $minSelected = 0;
	public ?int $maxSelected = null;
	public function minSelected(int $minSelected) : self
	{
		$this->minSelected = $minSelected;
		return $this;
	}
	
	public function maxSelected(int $maxSelected) : self
	{
		$this->maxSelected = $maxSelected;
		return $this->multiple($maxSelected > 1);
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		$rendered = $this->displayVar($this->getVar());
		if ($label = $this->renderLabel())
		{
			return "{$label}: {$rendered}";
		}
		return $rendered;
	}
	
	public function renderCell() : string
	{
		return $this->displayChoice($this->getVar());
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('Core', 'select_cell.php', ['field' => $this]);
	}
	
	public function renderForm() : string
	{
		return GDT_Template::php('Core', 'select_form.php', ['field' => $this]);
	}
	
	public function renderEmptyLabel() : string
	{
		if (isset($this->emptyLabelRaw))
		{
			return $this->emptyLabelRaw;
		}
		elseif (isset($this->emptyLabelKey))
		{
			return t($this->emptyLabelKey, $this->emptyLabelArgs);
		}
		else
		{
			return ' - none - ';
		}
	}
	
	public function htmlSelected(string $var) : string
	{
		if ($this->multiple)
		{
			if ($selected = @json_decode($this->var, true))
			{
				if (in_array($var, $selected, true))
				{
					return self::SELECTED;
				}
			}
			return GDT::EMPTY_STRING;
		}
		else
		{
			return $this->getVar() === $var ? self::SELECTED : '';
		}
	}
	
	public function htmlMultiple() : string
	{
		return $this->multiple ? ' multiple="multiple" size="8"' : '';
	}
	
	public function htmlName() : string
	{
		if ($name = $this->getName())
		{
			$mul = $this->multiple ? '[]' : '';
			return sprintf(' name="%s%s"', $name, $mul);
		}
		return '';
	}
	
	public function htmlChoiceVar($var, $value) : string
	{
		if ($value === null)
		{
			return '';
		}
		if (is_string($value))
		{
			$var = html($var);
		}
		else
		{
			$var = $value->getID();
		}
		return sprintf(' value="%s"', $var);
	}
	
	public function configJSON() : array
	{
		return array_merge(parent::configJSON(), [
			'multiple' => $this->multiple,
			'selected' => $this->multiple ? $this->getValue() : $this->getSelectedVar(),
			'minSelected' => $this->minSelected,
			'maxSelected' => $this->maxSelected,
		    'emptyVar' => $this->emptyVar,
		    'emptyLabel' => $this->displayEmptyLabel(),
		]);
	}
	
	public function displayChoice($choice) : string
	{
		if (is_string($choice) || ($choice === null))
		{
			return $this->displayVar($choice);
		}
		else
		{
			return $choice->renderOption();
		}
	}
	
	public function displayVar(string $var=null) : string
	{
		if ($var === null)
		{
			return TextStyle::italic(t('none'));
		}
		$this->initChoices();
		
		if (!isset($this->choices[$var]))
		{
			return GDT::EMPTY_STRING;
		}
		
		$value = $this->choices[$var];
		
		if (is_string($value))
		{
			return $value;
		}
		
		return $value->renderName();
	}
	
	public function renderFilter(GDT_Filter $f) : string
	{
		if ($this->hasCompletion())
		{
			return GDT_Template::php('Core', 'combobox_filter.php', ['field' => $this,  'f' => $f]);
		}
		else 
		{
			return GDT_Template::php('Core', 'select_filter.php', ['field' => $this,  'f' => $f]);
		}
	}
	
	public function plugVars() : array
	{
		$result = [];
		foreach (array_keys($this->initChoices()) as $choice)
		{
			$result[] = $choice;
			if (count($result) >= 2)
			{
				break;
			}
		}
		$back = [];
		foreach ($result as $plug)
		{
			$back[] = [$this->getName() => $plug];
		}
		return $back;
	}
	
}
