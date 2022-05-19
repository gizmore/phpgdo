<?php
namespace GDO\Core;

/**
 * This trait adds initial/input/var/value schema to a GDT.
 * The very base GDT does not have this.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
trait WithValue
{
	################
	### Required ###
	################
	public function required(bool $required = true)
	{
		return $this->notNull($required);
	}
	
	###########################
	### Input / Var / Value ###
	###########################
	public bool $valueConverted = false; # Has var been converted to value already?
	public ?string $initial = null; # initial var
	public ?string $input = null; # input string
	public ?string $var = null; # input db var
	public $value; # output value
	
	public function initial(string $initial = null) : self
	{
		$this->initial = $initial;
		return $this->var($initial);
	}

	public function input(string $input = null) : self
	{
		$this->input = $input;
		return $this;
	}

	public function hasInput() : bool
	{
		return !empty($this->input);
	}
	
	public function var(string $var = null) : self
	{
		$this->var = $var;
		$this->valueConverted = false;
		return $this;
	}
	
	public function value($value) : self
	{
		$this->var = $this->toVar($value);
		$this->value = $value;
		$this->valueConverted = true;
		return $this;
	}
	
	public function getInitial() : ?string
	{
		return $this->initial;
	}
	
	public function getVar() : ?string
	{
		return $this->var;
	}
	
	public function getValue()
	{
		if (!$this->valueConverted)
		{
			$this->value = $this->toValue($this->var);
			$this->valueConverted = true;
		}
		return $this->value;
	}

	##################
	### Positional ###
	##################
	public function isPositional() : bool
	{
		return $this->isRequired() && (!$this->initial);
	}

}
