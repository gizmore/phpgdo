<?php
declare(strict_types=1);
namespace GDO\Table;

use GDO\Core\GDT_Template;

/**
 * Similiar to a table, a list displays multiple cards or list items.
 *
 * Control ->itemTemplate(GDT) which defaults to GDT_GDO.
 * Control ->listMode(1|2) for cards or list items.
 *
 * @version 7.0.3
 * @since 5.0
 *
 * @author gizmore
 * @see GDT_GDO
 * @see GDT_Table
 */
class GDT_ListCard extends GDT_List
{

	public function renderHTML(): string
	{
		return GDT_Template::php('Table', 'list_card.php', ['field' => $this]);
	}

}
