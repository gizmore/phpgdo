<?php
declare(strict_types=1);
namespace GDO\Table;

use GDO\Core\GDT_UInt;
use GDO\DB\Query;

/**
 * Simple row number counter++
 *
 * @version 7.0.3
 * @since 6.3.0
 * @author gizmore
 */
class GDT_Count extends GDT_UInt
{

	private int $num = 1;

	public function isVirtual(): bool { return true; }

	public function isOrderable(): bool { return false; }

	public function gdtDefaultLabel(): ?string
    { return null; }

	public function render(): array|string|null
	{
		return (string) ($this->num++);
	}

	public function searchQuery(Query $query, string $searchTerm): static
	{
		return $this;
	}

}
