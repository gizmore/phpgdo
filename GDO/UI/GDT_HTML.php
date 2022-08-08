<?php
namespace GDO\UI;

use GDO\Core\WithValue;

/**
 * Very simple field that only has custom html content.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.7.0
 */
final class GDT_HTML extends GDT_Container
{
	use WithValue;
	
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

	public static function withHTML(?string $html) : self
	{
		return self::make()->var($html);
	}

	public function renderCell() : string
	{
		return $this->getVar() . parent::renderCell();
	}
	
	public function renderCard() : string
	{
	    return "<div class=\"gdt-html\">{$this->renderCell()}</div>";
	}
	
}
