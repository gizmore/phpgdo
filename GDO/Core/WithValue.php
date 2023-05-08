<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\WithNullable;

/**
 * This trait adds initial/input/var/value schema to a GDT.
 * The very base GDT do not even have this.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 */
trait WithValue
{

	final public const HTML_REQUIRED = ' required="required"';

	use WithNullable;

	###########################
	### Input / Var / Value ###
	###########################

	public ?string $initial = null;

	public ?string $var = null;

	public bool $valueConverted = false;

	public bool|int|float|string|array|null|object $value = null;

	/**
	 * Render HTML required attribute.
	 */
	public function htmlRequired(): string
	{
		return $this->notNull ? self::HTML_REQUIRED : GDT::EMPTY_STRING;
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

	public function initial(?string $initial): static
	{
		$this->initial = $initial;
		return $this->var($initial);
	}

	public function var(?string $var): static
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

	public function reset(): static
	{
		unset($this->errorRaw);
		unset($this->errorKey);
		unset($this->errorArgs);
		return $this->var($this->initial);
	}

	/**
	 * Render html value attribute value="foo".
	 */
	public function htmlValue(): string
	{
		if (null === ($var = $this->getVar()))
		{
			return GDT::EMPTY_STRING;
		}
		$var = html($var);
		return " value=\"{$var}\"";
	}

	public function getVar(): string|array|null
	{
		if (null !== ($input = $this->getInput()))
		{
			$this->var($this->inputToVar($input));
		}
		return $this->var;
	}

	public function getValue(): bool|int|float|string|array|null|object
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

	public function hasChanged(): bool
	{
		return $this->getVar() !== $this->initial;
	}

//	public  function addInputValue($value): GDT
//	{
//		$this->addInput($this->getName(), (string)$value);
//		return $this->value($value);
//	}

	/**
	 * Setup this GDT from a GDO.
	 */
	public function gdo(?GDO $gdo): static
	{
		if ($gdo)
		{
			if ($gdo->gdoIsTable())
			{
				return $this->var($this->initial);
			}
			return $this->var($gdo->gdoVar($this->name));
		}
		return $this->var(null);
	}


	public function setGDOData(array $data): static
	{
		return $this->var($data[$this->name] ?? null);
	}

	##################
	### Positional ###
	##################
	/**
	 * Positional GDT cannot be referenced by name in GDT_Expressions.
	 */
	public function isPositional(): bool
	{
		return $this->isRequired() && ($this->initial === null);
	}

}
