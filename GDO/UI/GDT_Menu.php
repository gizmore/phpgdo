<?php
namespace GDO\UI;

use GDO\Core\WithFields;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_Fields;

/**
 * A popup menu
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 */
final class GDT_Menu extends GDT_Fields
{
	use WithLabel;
	use WithFields;
	
	public function defaultName() { return 'menu'; }
	
	public function renderCell() : string { return GDT_Template::php('UI', 'cell/menu.php', ['field'=>$this]); }
	
}
