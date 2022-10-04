<?php
namespace GDO\Core;

/**
 * Index db column definition.
 * The default algo is HASH. BTREE available.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.5.0
 */
class GDT_Index extends GDT
{
	###########
	### GDT ###
	###########
	public function gdoColumnDefine() : string
	{
	    return "{$this->fulltextDefine()} INDEX({$this->indexColumns}) {$this->usingDefine()}";
	}
	
	private function fulltextDefine() : string
	{
	    return $this->indexFulltext;
	}
	
	private function usingDefine()
	{
	    return $this->indexUsing === false ? '' : $this->indexUsing;
	}
	
	###############
	### Columns ###
	###############
	private string $indexColumns;
	public function indexColumns(string... $indexColumns)
	{
	    $this->indexColumns = implode(',', $indexColumns);
	    return $this;
	}
	
	##################
	### Index Type ###
	##################
	const FULLTEXT = 'FULLTEXT';
	const HASH = 'USING HASH';
	const BTREE = 'USING BTREE';
	private $indexFulltext = false;
	private $indexUsing = self::HASH;
	public function hash() { $this->indexUsing = self::HASH; return $this; }
	public function btree() { $this->indexUsing = self::BTREE; return $this; }
	public function fulltext() { $this->indexFulltext = self::FULLTEXT; return $this; }
	
}
