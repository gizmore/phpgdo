<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;

/**
 * @version 7.0.1
 * @since 5.0
 *
 * @author gizmore
 * @see GDT_GDO
 * @see GDT_Table
 */
class GDT_List extends GDT_Table
{

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		return GDT_Template::php('Table', 'list_html.php', ['field' => $this]);
	}

}
