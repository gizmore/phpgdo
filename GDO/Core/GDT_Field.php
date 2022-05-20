<?php
namespace GDO\Core;

use GDO\DB\WithNullable;
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
 * @version 7.0.0
 * @since 7.0.0
 */
abstract class GDT_Field extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithValue;
	use WithError;
	use WithNullable;

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
	public function inputToVar(string $input) : string
	{
		if (is_string($input))
		{
			$input = trim($input);
		}
		return ($input === null) || ($input === '') ?
			null : $input;
	}
	
	##################
	### Permission ###
	##################
	public bool $hidden = false;
	public function hidden(bool $hidden = true) : self { $this->hidden = $hidden; return $this; }
	public function isHidden() : bool { return $this->hidden; }
	
	public bool $readable = true;
	public function readable(bool $readable) : self { $this->readable = $readable; return $this; }
	public function isReadable() : bool { return $this->readable; }
	
	public bool $writeable = true;
	public function writeable(bool $writeable) : self { $this->writeable = $writeable; return $this; }
	public function isWritable() : bool { return $this->writeable; }
	
	public bool $focusable = false;
	public function focusable(bool $focusable = true) : self { $this->focusable = $focusable; return $this; }
	public function isFocusable() : bool { return $this->focusable; }	
	
	################
	### Features ###
	################
	public function isOrderable() : bool { return true; }
	public function isSearchable() : bool { return true; }
	public function isFilterable() : bool { return true; }
	public function isSerializable() : bool { return true; }
	
}
