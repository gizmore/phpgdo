<?php
namespace GDO\Table;

/**
 * A method that displays a list.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
abstract class MethodList extends MethodTable
{
	public function createCollection() : GDT_Table
	{
		$this->table = GDT_List::make();
		return $this->table;
	}

}
