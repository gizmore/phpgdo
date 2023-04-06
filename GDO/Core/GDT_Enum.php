<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * An enum.
 * It is a select with special rendering.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Enum extends GDT_Select
{

//	public string $emptyVar = 'none';

	public array $enumValues;

	public function enumValues(string...$enumValues): static
	{
		$this->enumValues = $enumValues;
		return $this;
	}

	public function getChoices(): array
	{
		$choices = [];
//		if (!$this->notNull)
//		{
//			$choices[$this->emptyVar] = $this->renderEmptyLabel();
//		}
		if (isset($this->enumValues))
		{
			$choices = array_merge($choices, array_combine($this->enumValues, $this->enumValues));
			return $choices;
		}
		return GDT::EMPTY_ARRAY;
	}

	public function configJSON(): array
	{
		return [
			'name' => $this->getName(),
			'enumValues' => $this->enumValues ?? null,
			'selected' => $this->getVar(),
			'notNull' => $this->notNull,
		];
	}

	public function displayVar(string $var = null): string
	{
		return $var === null ? self::none() : t('enum_' . $var);
	}

	public function gdoExampleVars(): ?string
	{
		return implode('|', $this->enumValues);
	}

	##############
	### Render ###
	##############

	public function enumIndex(): int
	{
		return $this->enumIndexFor($this->getVar());
	}

	public function enumIndexFor($enumValue): int
	{
		$index = array_search($enumValue, $this->enumValues, true);
		return $index === false ? 0 : $index + 1;
	}

}
