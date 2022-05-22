<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A horizontal divider tag.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_Divider extends GDT
{
	use WithLabel;
	
	public function renderHTML() : string
	{
	    return '<div class="gdt-divider">'.$this->renderLabel().'</div>';
	}
	
}
