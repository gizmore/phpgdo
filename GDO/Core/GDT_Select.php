<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Table\GDT_Filter;
use GDO\UI\TextStyle;
use GDO\Util\Arrays;

/**
 * An HTML select.
 * Can autocomplete input, like `./gdo.sh mail.send giz <.....>`.
 * Validates min/max selected.
 * Fill choices in getChoices()
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Select extends GDT_ComboBox
{

	/**
	 * For options
	 */
	final public const SELECTED = ' selected="selected"';

	###################
	### Var / Value ###
	###################

	public string $icon = 'select';

	public string $emptyVar = GDT::ZERO;
	public string $emptyLabelRaw;
	public string $emptyLabelKey;	public function getSelectedVar(): ?string
	{
		$var = $this->getVar();
		return $var === null ? $this->emptyVar : $var;
	}
	public ?array $emptyLabelArgs;
	public bool $multiple = false;
	public ?int $minSelected = 0;
	public ?int $maxSelected = 1;

	public function emptyVar(string $emptyVar): static
	{
		$this->emptyVar = $emptyVar;
		return $this;
	}

	public function emptyLabelRaw(string $text): static
	{
		$this->emptyLabelRaw = $text;
		unset($this->emptyLabelKey);
		unset($this->emptyLabelArgs);
		return $this;
	}

	public function getValue(): mixed
	{
		if ($this->valueConverted)
		{
			return $this->value;
		}
		$var = $this->getVar();
		if ($var === null)
		{
			$value = $this->multiple ? GDT::EMPTY_ARRAY : null;
		}
		else
		{
			$value = $this->toValue($var);
		}
		$this->valueConverted = true;
		$this->value = $value;
		return $value;
	}

	public function hasEmptyLabel(): bool
	{
		return isset($this->emptyLabelRaw) || isset($this->emptyLabelKey);
	}

	public function emptyInitial(string $labelKey, string $emptyVar = GDT::ZERO): static
	{
		return $this->emptyLabel($labelKey)->initial($emptyVar);
	}

	public function emptyLabel(string $key, array $args = null): static
	{
		unset($this->emptyLabelRaw);
		$this->emptyLabelKey = $key;
		$this->emptyLabelArgs = $args;
		return $this;
	}

	public function inputToVar(array|string|null|GDT_Method $input): ?string
	{
		return parent::inputToVar($input === $this->emptyVar ? null : $input);
	}

	public function minSelected(int $minSelected): static
	{
		$this->minSelected = $minSelected;
		return $this;
	}

	public function maxSelected(null|int $maxSelected): static
	{
		$this->maxSelected = $maxSelected;
		return $this->multiple(($maxSelected > 1)||($maxSelected === null));
	}

	public function multiple(bool $multiple = true): static
	{
		$this->multiple = $multiple;
		return $this;
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		if ($value === null)
		{
			return null;
		}

		# Multiple var
		if ($this->multiple)
		{
			return json_encode(array_values($value));
		}

		# Single var
		if ($value === $this->emptyVar)
		{
			return null;
		}

		if (false !== ($var = array_search($value, $this->initChoices(), true)))
		{
			return $var;
		}

		return null;
	}

	public function renderEmptyLabel(): string
	{
		if (isset($this->emptyLabelRaw))
		{
			return $this->emptyLabelRaw;
		}
		elseif (isset($this->emptyLabelKey))
		{
			return t($this->emptyLabelKey, $this->emptyLabelArgs);
		}
		else
		{
			return t('none');
		}
	}

	public function htmlSelected(string $var): string
	{
		if ($this->multiple)
		{
			if ($var)
			{
				$selected = json_decode($var, true);
				if (in_array($var, Arrays::arrayed($selected), true))
				{
					return self::SELECTED;
				}
			}
			return GDT::EMPTY_STRING;
		}
		else
		{
			return $this->getVar() === $var ? self::SELECTED : GDT::EMPTY_STRING;
		}
	}

	public function getVar(): string|array|null
	{
		if (null === ($var = parent::getVar()))
		{
			return $this->multiple ? '[]' : null;
		}
		elseif ($this->multiple)
		{
			return is_array($var) ? json_encode($var) : $var; # NO visible json, no pretty print.
		}
		else
		{
			return $var;
		}
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $this->selectToValue($var);
	}

	public function htmlMultiple(): string
	{
		return $this->multiple ? ' multiple="multiple" size="8"' : '';
	}

	public function htmlChoiceVar(string $var, $value): string
	{
		if ($value === null)
		{
			return GDT::EMPTY_STRING;
		}
		if (is_string($value))
		{
			$var = html($var);
		}
		else
		{
			$var = $value->getID();
		}
		return " value=\"{$var}\"";
	}

	public function selectToValue(?string $var)
	{
		if ($var === null)
		{
			return null;
		}
		if ($this->multiple)
		{
			return json_decode($var, true);
		}
		if ($var === $this->emptyVar)
		{
			return $var;
		}
		$this->initChoices();
		return $this->choices[$var] ?? $this->toClosestChoiceValue($var);
	}

	protected function getChoices(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	public function initChoices(): array
	{
		if (!isset($this->choices))
		{
			$this->choices($this->getChoices());
		}
		return $this->choices;
	}


	protected function toClosestChoiceValue($var): null|string|GDO
	{
		$candidatesZero = [];
		$candidatesMiddle = [];
		$this->initChoices();
		foreach ($this->choices as $vaar => $value)
		{
			$name = is_string($value) ? $value : $value->getName();

			if (strcasecmp($var, $name) === 0)
			{
				return $value;
			}

			$pos = stripos((string)$vaar, $var);
			if ($pos === false)
			{
				if (false === ($pos = stripos($name, $var)))
				{
					continue;
				}
			}
			if ($pos === 0)
			{
				$candidatesZero[] = $value;
			}
			$candidatesMiddle[] = $value;
		}

		if (count($candidatesZero) === 1)
		{
			return $candidatesZero[0];
		}

		if (count($candidatesMiddle) === 1)
		{
			return $candidatesMiddle[0];
		}

		if (count($candidatesMiddle) > 1)
		{
			$candidatesMiddle = array_slice($candidatesMiddle, 0, 5);
			foreach ($candidatesMiddle as $i => $candidate)
			{
				if (is_object($candidate))
				{
					$candidatesMiddle[$i] = $candidate->renderName();
				}
			}
			$this->error('err_select_candidates', [implode('|', $candidatesMiddle)]);
		}

		return null;
	}


	public function getGDOData(): array
	{
		$var = $this->getVar();
		return (($var === null) || ($var === $this->emptyVar)) ?
			GDT::EMPTY_ARRAY : [$this->name => $var];
	}

	################
	### Validate ###
	################


	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		return $this->multiple ?
			$this->validateMultiple($value) :
			$this->validateSingle($value);
	}


	private function validateMultiple(?array $values): bool
	{
		if ($values === null)
		{
			return $this->validateNull($values);
		}

//		if ($values)
//		{
			foreach ($values as $value)
			{
				if (!$this->validateSingle($value))
				{
					return false;
				}
			}
//		}

		if ((isset($this->minSelected)) && (count($values) < $this->minSelected))
		{
			return $this->error('err_select_min', [$this->minSelected]);
		}

		if ((isset($this->maxSelected)) && (count($values) > $this->maxSelected))
		{
			return $this->error('err_select_max', [$this->maxSelected]);
		}

		return true;
	}


	protected function validateSingle($value): bool
	{
		if ($value === null)
		{
			return !$this->notNull || $this->errorNull();
		}

		if ($value === $this->emptyVar)
		{
			return !$this->notNull || $this->errorNull();
		}

		$this->initChoices();

		if ($value instanceof GDO)
		{
			return isset($this->choices[$value->getID()]) ?? $this->errorInvalidChoice();
		}

		if ($value instanceof GDT)
		{
			return isset($this->choices[$value->getVar()]) ?? $this->errorInvalidChoice();
		}

		if (in_array($value, $this->choices, true)) # check single identity
		{
			return true;
		}

		return $this->errorInvalidChoice();
	}


	protected function errorInvalidChoice(): bool
	{
		return $this->error('err_invalid_choice');
	}

	#############
	### Empty ###
	#############

	public function displayEmptyLabel(): string
	{
		if (isset($this->emptyLabelRaw))
		{
			return $this->emptyLabelRaw;
		}
		if (isset($this->emptyLabelKey))
		{
			return t($this->emptyLabelKey, $this->emptyLabelArgs);
		}
		return GDT::EMPTY_STRING;
	}


	##############
	### Render ###
	##############
	public function renderCLI(): string
	{
		$rendered = $this->displayVar($this->getVar());
		if ($label = $this->renderLabel())
		{
			return "{$label}: {$rendered}";
		}
		return $rendered;
	}

	public function renderCell(): string
	{
		return $this->displayChoice($this->getVar());
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('Core', 'select_cell.php', ['field' => $this]);
	}

	public function renderCard(): string
	{
		$rendered = $this->displayVar($this->getVar());
		return $this->displayCard($rendered);
	}

	public function renderForm(): string
	{
		return GDT_Template::php('Core', 'select_form.php', ['field' => $this]);
	}


	public function htmlName(): string
	{
		if ($name = $this->getName())
		{
			$mul = $this->multiple ? '[]' : '';
			return sprintf(' name="%s%s"', $name, $mul);
		}
		return GDT::EMPTY_STRING;
	}


	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'multiple' => $this->multiple,
			'selected' => $this->multiple ? $this->getValue() : $this->getSelectedVar(),
			'minSelected' => $this->minSelected,
			'maxSelected' => $this->maxSelected,
			'emptyVar' => $this->emptyVar,
			'emptyLabel' => $this->displayEmptyLabel(),
		]);
	}

	public function displayChoice($choice): string
	{
		if (is_string($choice) || ($choice === null))
		{
			return $this->displayVar($choice);
		}
		else
		{
			return $choice->renderOption();
		}
	}

	public function displayVar(string $var = null): string
	{
		if ($var === null)
		{
			return TextStyle::italic(t('none'));
		}
		$this->initChoices();

		if (!isset($this->choices[$var]))
		{
			return GDT::EMPTY_STRING;
		}

		$value = $this->choices[$var];

		if (is_string($value))
		{
			return $value;
		}

		return $value->renderName();
	}

	public function renderFilter(GDT_Filter $f): string
	{
		if ($this->hasCompletion())
		{
			return GDT_Template::php('Core', 'combobox_filter.php', ['field' => $this, 'f' => $f]);
		}
		else
		{
			return GDT_Template::php('Core', 'select_filter.php', ['field' => $this, 'f' => $f]);
		}
	}

	public function plugVars(): array
	{
		$result = [];
		foreach (array_keys($this->initChoices()) as $choice)
		{
			$result[] = $choice;
			if (count($result) >= 2)
			{
				break;
			}
		}
		$back = [];
		foreach ($result as $plug)
		{
			$back[] = [$this->getName() => $plug];
		}
		return $back;
	}

	public function gdoExampleVars(): ?string
	{
		$back = [];
		$choices = $this->initChoices();
		foreach (array_keys($choices) as $var)
		{
			$back[] = $var;
		}
		return implode('|', $back);
	}

}
