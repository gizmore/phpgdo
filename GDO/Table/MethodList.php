<?php
declare(strict_types=1);
namespace GDO\Table;

/**
 * A method that displays a list.
 *
 * @version 7.0.3
 * @since 6.1.0
 * @author gizmore
 */
abstract class MethodList extends MethodTable
{

	public function createCollection(): GDT_Table
	{
		$this->table = GDT_List::make();
		return $this->table;
	}

}
