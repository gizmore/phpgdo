<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Index db column definition.
 * The default algo is HASH. BTREE available.
 *
 * @version 7.0.3
 * @since 6.5.0
 * @author gizmore
 */
final class GDT_Index extends GDT
{

	use WithName;

	final public const FULLTEXT = 'FULLTEXT';
	final public const HASH = 'USING HASH';
	final public const BTREE = 'USING BTREE';

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

	/**
	 * Set the columns to index.
	 * Set's GDT name to regarding value, if not set yet.
	 */
	public function indexColumns(string...$indexColumns): self
	{
		$this->indexColumns = implode(',', $indexColumns);
		return $this->name ? $this :
			$this->name(str_replace(',', '_', $this->indexColumns));
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

	public function fulltext(): self
	{
		$this->indexFulltext = self::FULLTEXT;
		return $this;
	}

}
