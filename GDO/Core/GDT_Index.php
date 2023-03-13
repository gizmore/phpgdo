<?php
namespace GDO\Core;

/**
 * Index db column definition.
 * The default algo is HASH. BTREE available.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.5.0
 */
class GDT_Index extends GDT
{
	use WithName;
	
	const FULLTEXT = 'FULLTEXT';
	const HASH = 'USING HASH';
	const BTREE = 'USING BTREE';
	
	###########
	### GDT ###
	###########
	public function isPrimary(): bool
	{
		# This fixes gdoPrimaryKeyColumns() for IP2Country
		return false;
	}
	
	public function renderHTML(): string
	{
		return GDT::EMPTY_STRING;
	}
	
	public function isVirtual(): bool
	{
		return true;
	}

	###############
	### Columns ###
	###############
	public string $indexColumns;
	
	public function indexColumns(string... $indexColumns): self
	{
	    $this->indexColumns = implode(',', $indexColumns);
	    # Default name if none is given?
	    $this->name = $this->getName() ?
	    	$this->name : str_replace(',', '_', $this->indexColumns);
	    return $this;
	}
	
	##################
	### Index Type ###
	##################
	public string $indexFulltext;
	public string $indexUsing = self::HASH;
	public function hash(): static
	{
		$this->indexUsing = self::HASH;
		return $this;
	}

	public function btree(): static
	{
		$this->indexUsing = self::BTREE; return $this;
	}

	public function fulltext()
	{
		$this->indexFulltext = self::FULLTEXT;
		return $this;
	}
	
}
