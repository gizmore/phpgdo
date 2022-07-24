<?php
namespace GDO\Core;

use GDO\Form\WithFormAttributes;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\UI\WithPHPJQuery;
use GDO\Form\GDT_Form;

/**
 * A GDT with user input.
 * 
 * Fields have a name and a value.
 * Fields have an optional error message.
 * Fields can be nullable.
 * Fields can have an icon, a label and a placeholder.
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
		return GDT::EMPTY_GDT_ARRAY;
	}
	
	############
	### Data ###
	############
	public function getGDOData() : ?array
	{
		$v = $this->getVar();
		return $v === null ? null : [$this->name => $v];
	}

	public function setGDOData(array $data) : self
	{
		if (isset($data[$this->name]))
		{
			return $this->initial($data[$this->name]);
		}
		return $this->var($this->initial);
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
	public bool $orderable = true;
	public bool $aclcapable = true;
	public bool $searchable = true;
	public bool $filterable = true;
	public function isOrderable() : bool { return $this->orderable; }
	public function isACLCapable() : bool { return $this->aclcapable; }
	public function isSearchable() : bool { return $this->searchable; }
	public function isFilterable() : bool { return $this->filterable; }
	public function isSerializable() : bool { return true; }
	
	public function orderable(bool $orderable) : self
	{
		$this->orderable = $orderable;
		return $this;
	}
	
	public function noacl() : self { return $this->aclcapable(false); }
	public function aclcapable(bool $aclcapable) : self
	{
		$this->aclcapable = $aclcapable;
		return $this;
	}
	
	public function searchable(bool $searchable) : self
	{
		$this->searchable = $searchable;
		return $this;
	}
	
	public function filterable(bool $filterable) : self
	{
		$this->filterable = $filterable;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function htmlFocus() : ?string
	{
		if ($form = GDT_Form::$CURRENT)
		{
			if ($form->focus)
			{
				if ($this->notNull)
				{
					if ($this->getVar() === null)
					{
						return ' data-focus';
					}
				}
			}
		}
		return null;
	}

}
