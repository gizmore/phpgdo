<?php
namespace GDO\UI;

/**
 * Very simple field that only has custom html content.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.7.0
 */
final class GDT_HTML extends GDT_Container
{
// 	##############
// 	### Render ###
// 	##############
// 	public function renderHTML() : string
// 	{
// 		$html = '';
// 	    $this->withFields(function(GDT $gdt) use(&$html) {
// 	    	$html .= $gdt->renderCell();
// 	    });
//     	return $html;
// 	}
	
	public function renderCard() : string
	{
	    return "<div class=\"gdt-html\">{$this->renderHTML()}</div>";
	}
	
}
