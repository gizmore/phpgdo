<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_String;

/**
 * A search field is a text with icon and default label.
 * Input type is set to search.
 * Field is auxillary and does not feature order, filter and search.
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 */
class GDT_SearchField extends GDT_String
{

	public string $icon = 'search';

	public ?int $min = 2;
	public ?int $max = 256;

	public bool $hidden = true;

	public function isCLIHidden(): bool { return false; }

	public function getDefaultName(): string { return 'search'; }

	public function defaultLabel(): self { return $this->label('search'); }

	public function getInputType(): string { return 'search'; }

	public function isSerializable(): bool { return false; }

	public function isOrderable(): bool { return false; }

	public function isSearchable(): bool { return false; }

	public function isFilterable(): bool { return false; }

	public function gdoExampleVars(): ?string
	{
		return t('search_term');
	}

	public function plugVars(): array
	{
		$name = $this->getName();
		return [
			[$name => 'giz'],
		];
	}

}
