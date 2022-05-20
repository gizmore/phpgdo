<?php
namespace GDO\Core;

use GDO\DB\WithNullable;
use GDO\Form\WithFormAttributes;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;

/**
 * Fields have a name and a value.
 * Fields have an optional error message.
 * Fields can be nullable.
 * 
 * The make method sets the name to default or specified parameter.
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
	use WithNullable;
	use WithFormAttributes;
	
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
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		return $this->validateNull($value);
	}
	
	public function isRequired() : bool
	{
		return $this->notNull;
	}
	
	#######################
	### Input/Var/Value ###
	#######################
	public function inputToVar(string $input=null) : ?string
	{
		if (is_string($input))
		{
			$input = trim($input);
			return $input === '' ? null : $input;
		}
		return null;
	}
	
	################
	### Features ###
	################
	public function isOrderable() : bool { return true; }
	public function isSearchable() : bool { return true; }
	public function isFilterable() : bool { return true; }
	public function isSerializable() : bool { return true; }
	
}
