<?php
namespace GDO\Core;

/**
 * An enum.
 * It is a select with special rendering.
 *
 * @version 7.0.1
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Enum extends GDT_Select
{

	public array $enumValues;

	public function enumValues(string...$enumValues): self
	{
		$this->enumValues = $enumValues;
		return $this;
	}

	public function getChoices(): array
	{
		if (isset($this->enumValues))
		{
			return array_combine($this->enumValues, $this->enumValues);
		}
		return GDT::EMPTY_ARRAY;
	}

	public function configJSON(): array
	{
		return [
			'name' => $this->getName(),
			'enumValues' => isset($this->enumValues) ? $this->enumValues : null,
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

	public function enumIndex()
	{
		return $this->enumIndexFor($this->getVar());
	}

	public function enumIndexFor($enumValue)
	{
		$index = array_search($enumValue, $this->enumValues, true);
		return $index === false ? 0 : $index + 1;
	}

}
