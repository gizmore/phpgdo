<?php
namespace GDO\Core;

use GDO\DB\WithNullable;

/**
 * This trait adds initial/input/var/value schema to a GDT.
 * The very base GDT do not even have this.
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
	
	public function initial(string $initial = null): static
	{
		$this->initial = $initial;
		return $this->var($initial);
	}

	public function var(string $var = null): static
	{
		$this->var = $var;
		$this->valueConverted = false;
		return $this;
	}
	
	public function value($value): static
	{
		$this->var = $this->toVar($value);
		$this->value = $value;
		$this->valueConverted = true;
		return $this;
	}
	
	public function reset(bool $removeInput=false): static
	{
		if ($removeInput)
		{
			unset($this->inputs);
		}
		if ($this->hasError())
		{
			unset($this->errorRaw);
			unset($this->errorKey);
			unset($this->errorArgs);
		}
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
		if (!$this->valueConverted)
		{
			$var = $this->var;
			if ($this->isWriteable())
			{
				$var = $this->getVar();
			}
			$this->value = $this->toValue($var);
			$this->valueConverted = true;
		}
		return $this->value;
	}
	
	public function hasChanged() : bool
	{
		return $this->var !== $this->getVar();
	}

	/**
	 * Setup this GDT from a GDO.
	 */
	public function gdo(GDO $gdo = null): static
	{
		if ($gdo)
		{
			if ($gdo->gdoIsTable())
			{
				return $this->var($this->initial);
			}
			if ($name = $this->getName())
			{
				return $this->var($gdo->gdoVar($name));
			}
		}
		return $this->var(null);
	}
	
	public function gdoInitial(GDO $gdo = null): static
	{
		if ($gdo)
		{
			if ($name = $this->getName())
			{
				return $this->initial($gdo->gdoVar($name));
			}
		}
		return $this->initial(null);
	}
	
	public function setGDOData(array $data): static
	{
		$n = $this->name;
		$this->var = isset($data[$n]) ? $data[$n] : null;
		return $this;
	}
	
// 	public function getGDOData() : array
// 	{
// 		return [$this->name => $this->getVar()];
// 	}
	
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
	/**
	 * Render html value attribute value="foo".
	 */
	public function htmlValue() : string
	{
		$var = $this->getVar();
		return $var === null ? GDT::EMPTY_STRING :
			sprintf(' value="%s"', html($var));
	}
	
// 	public function renderHeader() : string
// 	{
// 		return $this->renderLabel();
// 	}
	
}
