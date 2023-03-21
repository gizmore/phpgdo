<?php
namespace GDO\Core;

/**
 * Index db column definition.
 * The default algo is HASH. BTREE available.
 *
 * @version 7.0.1
 * @since 6.5.0
 * @author gizmore
 */
class GDT_Index extends GDT
{

	use WithName;

	public const FULLTEXT = 'FULLTEXT';
	public const HASH = 'USING HASH';
	public const BTREE = 'USING BTREE';

	###########
	### GDT ###
	###########
	public string $indexColumns;
	public string $indexFulltext;
	public string $indexUsing = self::HASH;

	###############
	### Columns ###
	###############

	public function isPrimary(): bool
	{
		# This fixes gdoPrimaryKeyColumns() for IP2Country
		return false;
	}

	public function renderHTML(): string
	{
		return GDT::EMPTY_STRING;
	}

	##################
	### Index Type ###
	##################

	public function isVirtual(): bool
	{
		return true;
	}

	public function indexColumns(string...$indexColumns): self
	{
		$this->indexColumns = implode(',', $indexColumns);
		# Default name if none is given?
		$this->name = $this->getName() ?
			$this->name : str_replace(',', '_', $this->indexColumns);
		return $this;
	}

	public function hash(): self
	{
		$this->indexUsing = self::HASH;
		return $this;
	}

	public function btree(): self
	{
		$this->indexUsing = self::BTREE;
		return $this;
	}

	public function fulltext()
	{
		$this->indexFulltext = self::FULLTEXT;
		return $this;
	}

}
