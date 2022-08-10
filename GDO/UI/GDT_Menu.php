<?php
namespace GDO\UI;

use GDO\Core\WithFields;
use GDO\Core\WithInput;

/**
 * A popup menu
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 */
final class GDT_Menu extends GDT_Container
{
	use WithLabel;
	use WithInput;
	use WithFields;
	
	public function getDefaultName() : string { return 'menu'; }
	
	protected function setupHTML(): void
	{
		$this->addClass('gdt-menu');
		$this->flexDirection = self::HORIZONTAL;
		parent::setupHTML();
	}
	
// 	public function renderFields(int $renderMode) : string
// 	{
// 		return sprintf("<div class=\"gdt-menu\">%s</div>\n", parent::renderHTML());
// 	}
	
}
