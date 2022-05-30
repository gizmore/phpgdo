<?php
namespace GDO\UI;

use GDO\Core\WithFields;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_Fields;
use GDO\Core\WithInput;

/**
 * A popup menu
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.4.0
 */
final class GDT_Menu extends GDT_Bar
{
	use WithLabel;
	use WithInput;
	use WithFields;
	
	public function getDefaultName() : string { return 'menu'; }
	
// 	public function renderCell() : string
// 	{
// 		return GDT_Template::php('UI', 'cell/menu.php', ['field'=>$this]);
// 	}
	
	public function renderFields() : string
	{
		return sprintf("<div class=\"gdt-menu\">%s</div>\n", parent::renderHTML());
	}
	
}
