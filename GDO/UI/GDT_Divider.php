<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A horizontal divider tag.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDT_Divider extends GDT
{
	use WithLabel;
	
	public function renderCLI() : string
	{
		return sprintf("===%s===\n", $this->renderLabelText());
	}
	
	public function renderHTML() : string
	{
		$text = $this->renderLabelText();
		$text = $text ? "<h5>{$text}</h5>" : $text;
	    return '<div class="gdt-divider">' . $text . '</div>';
	}
	
// 	public function renderCell() : string
// 	{
// 		return '<div class="gdt-divider">'.$this->renderLabelText().'</div>';
// 	}
	
}
