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
	public function gdoColumnDefine() : string
	{
	    return "{$this->fulltextDefine()} INDEX({$this->indexColumns}) {$this->usingDefine()}";
	}
	
	private function fulltextDefine(): string
	{
		return isset($this->indexFulltext) ? $this->indexFulltext : GDT::EMPTY_STRING;
	}
	
	private function usingDefine()
	{
	    return $this->indexUsing === false ? '' : $this->indexUsing;
	}
	
	public function renderHTML(): string
	{
		return GDT::EMPTY_STRING;
	}
	
	###############
	### Columns ###
	###############
	private string $indexColumns;
	
	public function indexColumns(string... $indexColumns)
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
	private string $indexFulltext;
	private $indexUsing = self::HASH;
	public function hash() { $this->indexUsing = self::HASH; return $this; }
	public function btree() { $this->indexUsing = self::BTREE; return $this; }
	public function fulltext() { $this->indexFulltext = self::FULLTEXT; return $this; }
	
}
