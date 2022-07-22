<?php
namespace GDO\Table;

/**
 * Abstract class that renders a list.
 * Not filtered by default.
 *
 * @author gizmore
 * @version 6.11.0
 * @since 5.0.0
 */
abstract class MethodQueryList extends MethodQueryTable
{
    public function isFiltered() { return false; }
    
    public function gdoHeaders() : array { return []; }
    
    public function listName() { return 'list'; }
	
	public function gdoListMode() { return GDT_List::MODE_LIST; }
	
	public function createCollection() : GDT_Table
	{
		$this->table = GDT_List::make($this->getTableName());
		$this->table->href($this->gdoTableHREF());
		$this->table->gdo($this->gdoTable());
		$this->table->fetchAs($this->gdoFetchAs());
		return $this->table;
	}
	
}
