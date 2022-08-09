<?php
namespace GDO\Core;

use GDO\DB\WithNullable;

/**
 * This trait adds initial/input/var/value schema to a GDT.
 * The very base GDT do not even have this.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
trait WithValue
{
	use WithNullable;
	
	################
	### Required ###
	################
	/**
	 * @deprecated use notNull().
	 * @param bool $required
	 * @return self
	 */
	public function required(bool $required = true) : self
	{
		return $this->notNull($required);
	}
	
	/**
	 * Render HTML required attribute.
	 */
	public function htmlRequired() : string
	{
		return $this->notNull ? ' required="required"' : '';
	}
	
	###########################
	### Input / Var / Value ###
	###########################
	public bool    $valueConverted = false; # remember value has been converted already
	public ?string $initial = null; # initial dbinput var
	public ?string $var = null; # dbinput var
	public         $value; # output value
	
	public function initial(string $initial = null) : self
	{
		$this->initial = $initial;
		return $this->var($initial);
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
	
	public function reset() : self
	{
		return $this->var($this->initial);
	}
	
	public function getInitial() : ?string
	{
		return $this->initial;
	}
	
	public function getVar()
	{
		$input = $this->getInput();
		if ($input !== null)
		{
			return $this->inputToVar($input);
		}
		else
		{
			return $this->var;
		}
	}
	
	public function getValue()
	{
// 		if (!$this->valueConverted)
// 		{
			$var = $this->getVar();
			$this->value = $this->toValue($var);
			$this->valueConverted = true;
// 		}
		return $this->value;
	}
	
	public function hasChanged() : bool
	{
		return $this->var !== $this->getVar();
	}

	/**
	 * Setup this GDT from a GDO.
	 * 
	 * @param GDO $gdo
	 * @return self
	 */
	public function gdo(GDO $gdo = null) : self
	{
		if ($gdo)
		{
// 			$this->gdo = $gdo;
			if ($name = $this->getName())
			{
				return $this->initial($gdo->gdoVar($name));
			}
		}
		return $this->var(null);
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
// 	/**
// 	 * Render the value as dbVar
// 	 * @deprecated naming sucks
// 	 * @return string
// 	 */
// 	public function renderHTML() : string
// 	{
// 		return isset($this->var) ? html($this->var) : '';
// 	}

	/**
	 * Render html value attribute value="foo".
	 */
	public function htmlValue() : string
	{
		$var = $this->getVar();
		return $var ? sprintf(' value="%s"', html($var)) : '';
	}
	
	public function renderHeader() : string
	{
		return $this->renderLabel();
	}
	
	public function setGDOData(array $data) : self
	{
		$n = $this->name;
		$this->var = isset($data[$n]) ? $data[$n] : null;
		return $this;
	}

}
