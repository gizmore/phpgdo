<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A label is not field. It has no values. Only label. Label rendering is called on render functions.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
class GDT_Label extends GDT
{
	use WithLabel;
	
	public function renderCLI() : string { return $this->renderLabel(); }
	public function renderXML() : string { return "<label>" . $this->renderLabel() . "</label>"; }
	public function renderHTML() : string { return "<label>" . $this->renderLabel() . "</label>"; }
	public function renderCard() : string { return $this->renderLabel(); }
	public function renderForm() : string { return $this->renderLabel(); }
	public function renderCell() : string { return $this->renderLabel(); }
	public function renderJSON() { return $this->renderLabel(); }
	
}
