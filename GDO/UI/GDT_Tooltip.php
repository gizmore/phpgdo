<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;

/**
 * A tooltip is a help icon with hover text.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.2.0
 */
class GDT_Tooltip extends GDT_Icon
{
	public $icon = 'help';

	public function renderCell() : string
	{
		return GDT_Template::php('UI', 'cell/tooltip.php', ['field'=>$this]);
	}

}
