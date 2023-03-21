<?php
namespace GDO\Table;

/**
 * Same stuff as list, just different templates.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore
 */
abstract class MethodQueryCards extends MethodQueryList
{

	public function createCollection(): GDT_Table
	{
		$this->table = GDT_ListCard::make($this->getTableName());
		return $this->createCollectionB();
	}

}
