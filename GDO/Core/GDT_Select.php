<?php
namespace GDO\Core;

/**
 * An HTML select.
 * Can autocomplete input, like `./gdo.sh mail.send giz <.....>`.
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
	
	public function getVar() : ?string
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
		if ($this->var === null)
		{
			return $this->multiple ? [] : null;
		}
		return parent::getValue();
	}

	public function toVar($value) : ?string
	{
		if ($this->multiple)
		{
		    if ($value)
		    {
		        return json_encode(array_values($value));
		    }
		    else
		    {
		        return null;
		    }
		}
		elseif ($value === $this->emptyVar) 
		{
			return null;
		}
		elseif (false === ($var = array_search($value, $this->choices, true)))
		{
			return null;
		}
		else
		{
			return $var;
		}
	}

	public function toValue(string $var = null)
	{
	    if ($var === null)
	    {
	        return null;
	    }
	    if ($this->multiple)
	    {
	        if (is_array($var))
	        {
	            return $var;
	        }
	        return json_decode($var);
	    }
	    if ($var === $this->emptyVar)
	    {
	        return null;
	    }
	    if (isset($this->choices[$var]))
	    {
	        return $this->choices[$var];
	    }
	    else
	    {
	        $value = $this->toClosestChoiceValue($var);
	        $var = $this->toVar($value);
	        $this->var($var);
	        return $value;
	    }
	}
	
	private function toClosestChoiceValue($var)
	{
	    $candidatesZero = [];
	    $candidatesMiddle = [];
	    foreach ($this->choices as $vaar => $value)
	    {
	        $pos = stripos($vaar, $var);
	        if ($pos === 0)
	        {
	            $candidatesZero[] = $value;
	            $candidatesMiddle[] = $value;
	        }
	        elseif ($pos > 1)
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
	        $candidates = array_map(function($value) {
	            return $value;
	        }, $candidatesMiddle);
            $candidates = array_slice($candidates, 0, 10);
	        $this->error('err_select_candidates', [implode('|', $candidates)]);
	    }
	}
	
	public function getGDOData() : ?array
	{
		return [$this->name => ($this->var === $this->emptyVar ? null : $this->var)];
	}
	
	public function setGDOData(GDO $gdo=null)
	{
	    return (!$gdo) || $gdo->gdoIsTable() ? $this->var($this->emptyVar) : parent::setGDOData($gdo);
	}
	
// 	public function displayValue($var)
// 	{
// 	    $value = $this->toValue($var);
// 	    if ($this->multiple)
// 	    {
// 	        $value = array_map(function($gdo){ 
// 	            return $this->renderChoice($gdo); },
// 	            $value);
// 	        return implode(', ', $value);
// 	    }
// 	    return $this->renderChoice($value);
// 	}
	
	################
	### Validate ###
	################
// 	private function fixEmptyMultiple()
// 	{
// 	    $f = $this->formVariable();
// 		if (isset($_REQUEST[$f]) && $this->multiple)
// 		{
// 			if (!isset($_REQUEST[$f][$this->name]))
// 			{
// 				$_REQUEST[$f][$this->name] = [];
// 			}
// 		}
// 	}
	
	public function validate($value) : bool
	{
		return $this->multiple ?
			$this->validateMultiple($value) :
			$this->validateSingle($value);
	}
	
	private function validateMultiple($values)
	{
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
		
		if ($this->minSelected > count($values))
		{
			return $this->error('err_select_min', [$this->minSelected]);
		}
		
		if ( ($this->maxSelected !== null) && ($this->maxSelected < count($values)) )
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
			return $this->notNull ? $this->errorNotNull() : true;
		}
		
		if (is_object($value))
		{
    		if (isset($this->choices[$value->getID()])) # check memcached by id
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

	public function emptyInitial(string $labelKey, string $emptyVar='0')
	{
		return $this->emptyLabel($labelKey)->initial($emptyVar);
	}

	################
	### Multiple ###
	################
	public bool $multiple = false;
	public function multiple(bool $multiple=true) : self
	{
		$this->multiple = $multiple;
		return $this;
	}
	
	public int $minSelected = 0;
	public ?int $maxSelected;
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
	/**
	 * Render a chosen value.
	 * This is probably a string or a GDO.  
	 * 
	 * @param GDO|string $choice
	 */
	public function displayChoice($choice) : string
	{
		if (is_string($choice))
		{
			return html($choice);
		}
		else
		{
			return $choice->renderChoice();
		}
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
			if ($selected = @json_decode($this->getVar()))
			{
				if (in_array($var, $selected, true))
				{
					return self::SELECTED;
				}
			}
			return '';
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
	
// 	public function configJSON() : array
// 	{
// 		return array_merge(parent::configJSON(), [
// 			'multiple' => $this->multiple,
// 			'selected' => $this->multiple ? $this->getValue() : $this->getSelectedVar(),
// 			'minSelected' => $this->minSelected,
// 			'maxSelected' => $this->maxSelected,
// 		    'emptyVar' => $this->emptyVar,
// 		    'emptyLabel' => $this->displayEmptyLabel(),
// 		]);
// 	}
	
// 	public function formName()
// 	{
// 	    $name = parent::formName();
// 	    return $this->multiple ? "{$name}[]" : $name;
// 	}
	
}
