<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Form\GDT_Form;
use GDO\Form\WithFormAttributes;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;
use GDO\UI\WithPHPJQuery;

/**
 * A GDT with user input.
 *
 * Fields have a name and an initial/var/value.
 * Fields have an optional error message.
 * Fields can be nullable.
 * Fields can have an icon, a label and a placeholder.
 * Fields can have various form attributes.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
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

	public bool $orderable = true;

	##################
	### Name Label ###
	##################
	public bool $aclcapable = true;

	public bool $searchable = true;

	################
	### Creation ###
	################
	public bool $filterable = true;

	############
	### Data ###
	############
	public ?bool $positional = null;

	public string $as;

	public function __wakeup()
	{
		$this->valueConverted = false;
		parent::__wakeup(); #PP#delete#
	}

	################
	### Validate ###
	################

//	public function gdtDefaultLabel(): ?string
//	{
//		return$this->getName();
//	}

	public function gdoColumnNames(): array
	{
		$name = $this->getName();
		return $name ? [$name] : GDT::EMPTY_ARRAY;
	}

	public function blankData(): array
	{
		if ($key = $this->getName())
		{
			return [$key => $this->getVar()];
		}
		return GDT::EMPTY_ARRAY;
	}

	#######################
	### Input/Var/Value ###
	#######################

	public function getVar(): string|array|null
	{
		$name = $this->getName();
		if (isset($this->inputs[$name]))
		{
			$input = $this->inputs[$name];
			return $this->inputToVar($input);
		}
		return $this->var;
	}

	public function inputToVar(array|string|null|GDT_Method $input): ?string
	{
		if (is_string($input))
		{
//			$input = trim($input);
			return ($input === GDT::EMPTY_STRING) ? null : $input;
		}
		elseif ($input === null)
		{
			return null;
		}
		elseif ($input instanceof GDT_Method)
		{
			return $input->execute()->render();
		}
		elseif (is_array($input))
		{
			return json_encode($input);
		}
		else
		{
			return (string) $input;
		}
	}

	################
	### Features ###
	################

	public function renderCLI(): string
	{
		$back = '';
		if ($label = $this->renderLabel())
		{
			$back .= $label . ': ';
		}
		$back .= $this->displayVar($this->getVar());
		return $back;
	} #

	public function getGDOData(): array
	{
		return [$this->name => $this->getVar()];
	} # produces 3 ACL settings per GDT

	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'name' => $this->getName(),
			'var' => $this->getVar(),
		]);
	} # is searched during big searches

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		return $this->validateNull($value);
	} #

	public function isOrderable(): bool { return $this->orderable; }

	public function isACLCapable(): bool { return $this->aclcapable; }

	public function isSearchable(): bool { return $this->searchable; }

	public function isFilterable(): bool { return $this->filterable; }

	public function isSerializable(): bool { return true; }

	public function renderList(): string
	{
		$text = $this->renderLabelText();
		if ($text)
		{
			$text .= ':&nbsp;';
		}
		$text .= $this->displayVar($this->getVar());
		return "<div>$text</div>";
	}

	public function renderCard(): string
	{
		return $this->displayCard($this->renderHTML());
	}

	public function displayCard($var): string
	{
		return sprintf("<label>%s%s:&nbsp;</label><span>%s</span>\n",
			$this->htmlIcon(), $this->renderLabelText(), $var);
	}

	public function getParameterAlias(): ?string
	{
		return $this->as ?? $this->name;
	}

	public function isPositional(): bool
	{
		return ($this->positional === null) ?
			($this->isRequired() && ($this->initial === null)) :
			($this->positional);
	}

	##############
	### Render ###
	##############

	public function isRequired(): bool
	{
		return $this->notNull;
	}

	public function classError(): string
	{
		return $this->hasError() ? ' has-error' : GDT::EMPTY_STRING;
	}

	public function setGDOData(array $data): static
	{
		if (isset($data[$this->name]))
		{
			return $this->var($data[$this->name]);
		}
		return $this->var($this->initial);
	}

	public function orderable(bool $orderable): static
	{
		$this->orderable = $orderable;
		return $this;
	}

	public function noacl(): static
	{
		return $this->aclcapable(false);
	}

	##################
	### Positional ###
	##################

	public function aclcapable(bool $aclcapable): static
	{
		$this->aclcapable = $aclcapable;
		return $this;
	}

	public function searchable(bool $searchable): static
	{
		$this->searchable = $searchable;
		return $this;
	}

	public function filterable(bool $filterable): static
	{
		$this->filterable = $filterable;
		return $this;
	}

	##########
	### As ###
	##########

	public function htmlFocus(): ?string
	{
		if ($form = GDT_Form::$CURRENT)
		{
			if ($form->focus)
			{
				if ($this->focusable && $this->notNull)
				{
					if ($this->getVar() === null)
					{
						if ($this->notNull)
						{
							return ' gdo-focus-required';
						}
						else
						{
							return ' gdo-focus';
						}
					}
				}
			}
		}
		return null;
	}

	public function positional(?bool $positional = true): static
	{
		$this->positional = $positional;
		return $this;
	}

	public function as(string $as): static
	{
		$this->as = $as;
		return $this;
	}

}
