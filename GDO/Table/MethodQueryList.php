<?php
namespace GDO\Table;

/**
 * Abstract class that renders a list.
 * Not filtered by default.
 *
 * @version 6.11.0
 * @since 5.0.0
 * @author gizmore
 */
abstract class MethodQueryList extends MethodQueryTable
{

	public function listName() { return 'list'; }

	public function gdoHeaders(): array
	{
		return $this->gdoTable()->gdoColumnsCache();
	}

	public function createCollection(): GDT_Table
	{
		$this->table = GDT_List::make($this->getTableName());
		return $this->createCollectionB();
	}

	protected function createCollectionB(): GDT_Table
	{
		$this->table->href($this->gdoTableHREF());
		$this->table->gdo($this->gdoTable());
		$this->table->fetchAs($this->gdoFetchAs());
		$this->table->query($this->getQuery());
		return $this->table;
	}

}
