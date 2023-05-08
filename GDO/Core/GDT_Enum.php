<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Util\WS;

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

	final public const MAX_EXAMPLE_VARS = 5;

	public array $enumValues = GDT::EMPTY_ARRAY;

	public function enumValues(string...$enumValues): static
	{
		$this->enumValues = $enumValues;
		return $this;
	}

	protected function getChoices(): array
	{
		return array_combine($this->enumValues, $this->enumValues);
	}

	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'name' => $this->getName(),
			'enumValues' => $this->enumValues,
			'selected' => $this->getVar(),
			'notNull' => $this->notNull,
		]);
	}

	public function displayVar(string $var = null): string
	{
		return $var === null ? self::none() : t('enum_' . $var);
	}

	public function gdoExampleVars(): ?string
	{
		$max = self::MAX_EXAMPLE_VARS;
		$count = count($this->enumValues);
		$app = $count > $max ? '|...' : '';
		$items = array_slice($this->enumValues, 0, min($max, $count));
		return implode('|', $items) . $app;
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

	public function enumForId(int $id): ?string
	{
		if ($id === 0)
		{
			return null;
		}
		return $this->enumValues[$id-1];
	}


	public function renderBinary(): string
	{
		return WS::wr8($this->enumIndex());
	}

}
