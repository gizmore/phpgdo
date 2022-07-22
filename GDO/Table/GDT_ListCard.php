<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;

/**
 * Similiar to a table, a list displays multiple cards or list items.
 * 
 * Control ->itemTemplate(GDT) which defaults to GDT_GDO.
 * Control ->listMode(1|2) for cards or list items.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0
 * 
 * @see GDT_GDO
 * @see GDT_Table
 */
class GDT_ListCard extends GDT_List
{
	public function renderCell() : string
	{
		return GDT_Template::php('Table', 'cell/list_card.php', ['field' => $this]);
	}
	
}
