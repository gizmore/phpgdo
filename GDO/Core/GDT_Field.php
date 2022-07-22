<?php
namespace GDO\Core;

use GDO\Form\WithFormAttributes;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\UI\WithPHPJQuery;

/**
 * A GDT with user input.
 * 
 * Fields have a name and a value.
 * Fields have an optional error message.
 * Fields can be nullable.
 * Fields can have an icon.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
abstract class GDT_Field extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithValue;
	use WithError;
	use WithInput;
	use WithPHPJQuery;
	use WithPlaceholder;
	use WithFormAttributes;
	
	##################
	### Name Label ###
	##################
	public function defaultLabel() : self
	{
		if ($name = $this->getName())
		{
			return $this->label($name);
		}
		return $this;
	}
	
	################
	### Creation ###
	################
	public function blankData() : array
	{
		if ($key = $this->getName())
		{
			return [$key => $this->getVar()];
		}
		return [$this->getVar()];
	}
	
	############
	### Data ###
	############
	public function getGDOData() : ?array
	{
		$v = $this->getVar();
		$v = ($v === null) || ($v === '') ? null : $v;
		return [$this->name => $v];
	}

	public function configJSON() : array
	{
		return [
			'name' => $this->getName(),
		];
	}
	
	################
	### Validate ###
	################
	public function validateInput($input, bool $throw=true) : bool
	{
		$var = $this->inputToVar($input);
		$value = $this->toValue($var);
		if (!$this->validate($value))
		{
			if ($throw)
			{
				throw new GDO_ArgException($this);
			}
			return false;
		}
		return true;
	}
	
	public function validate($value) : bool
	{
		return $this->validateNull($value);
	}
	
	public function isRequired() : bool
	{
		return $this->notNull;
	}
	
	public function classError() : string
	{
		return $this->hasError() ? ' has-error' : '';
	}
	
	#######################
	### Input/Var/Value ###
	#######################
	public function inputToVar($input=null) : ?string
	{
		if (is_string($input))
		{
			$input = trim($input);
			return $input === '' ? $this->initial : $input;
		}
		if ($input instanceof GDT_Method)
		{
			return $input->execute()->renderCLI();
		}
		if (is_array($input))
		{
			return json_encode($input);
		}
		return $this->initial;
	}
	
	public function getVar()
	{
		if (isset($this->input))
		{
			return $this->inputToVar($this->input);
		}
		return $this->var;
	}
	
	################
	### Features ###
	################
	public function isOrderable() : bool { return true; }
	public function isSearchable() : bool { return true; }
	public function isFilterable() : bool { return true; }
	public function isSerializable() : bool { return true; }

	##############
	### Render ###
	##############
	public function htmlFocus() : ?string
	{
		if ($this->getVar() === null)
		{
			return ' data-focus';
		}
		return null;
	}

}
