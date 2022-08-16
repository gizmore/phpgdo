<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithValue;

/**
 * Very simple field that only has custom html content.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.7.0
 */
// final class GDT_HTML extends GDT_Container
final class GDT_HTML extends GDT
{
	use WithValue;
	
// 	##############
// 	### Render ###
// 	##############
// 	public function renderHTML() : string
// 	{
// 		$html = '';
// 	    $this->withFields(function(GDT $gdt) use(&$html) {
// 	    	$html .= $gdt->renderHTML();
// 	    });
//     	return $html;
// 	}

// 	public static function withHTML(?string $html) : self
// 	{
// 		return self::make()->var($html);
// 	}

// 	public function render() : string
// 	{
// 		return $this->getVar() . parent::renderHTML();
// 	}
	
// 	public function renderCard() : string
// 	{
// 	    return "<div class=\"gdt-html\">{$this->renderHTML()}</div>";
// 	}
	
}
