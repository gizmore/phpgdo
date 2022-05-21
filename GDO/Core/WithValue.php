<?php
namespace GDO\Core;

use GDO\DB\WithNullable;

/**
 * This trait adds initial/input/var/value schema to a GDT.
 * The very base GDT do not even have this.
 * 
 * The lifecycle of a GDT is as follows;
 * 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
trait WithValue
{
	use WithNullable;
	
	################
	### Required ###
	################
	public function required(bool $required = true) : self
	{
		return $this->notNull($required);
	}
	
	###########################
	### Input / Var / Value ###
	###########################
	public bool    $valueConverted = false; # remember value has been converted already
	public ?string $initial = null; # initial dbinput var
	public ?string $input = null; # userinput string
	public ?string $var = null; # dbinput var
	public         $value; # output value
	
	public function initial(string $initial = null) : self
	{
		$this->initial = $initial;
		return $this->var($initial);
	}

	public function input($input = null) : self
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
		$input = $this->getInput();
		if ($input !== null)
		{
			$var = $this->inputToVar($input);
		}
		else
		{
			$var = $this->var;
		}
		return $var;
	}
	
	public function getValue()
	{
		if (!$this->valueConverted)
		{
			$this->value = $this->toValue($this->getVar());
			$this->valueConverted = true;
		}
		return $this->value;
	}

	/**
	 * Setup this GDT from a GDO.
	 * 
	 * @param GDO $gdo
	 * @return self
	 */
	public function gdo(GDO $gdo = null) : self
	{
		return $this->var($gdo->gdoVar($this->name));
	}
	
	##################
	### Positional ###
	##################
	/**
	 * Positional GDT cannot be referenced by name in GDT_Expressions.
	 * 
	 * @return bool
	 */
	public function isPositional() : bool
	{
		return $this->isRequired() && ($this->initial === null);
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return html($this->var);
	}

}
