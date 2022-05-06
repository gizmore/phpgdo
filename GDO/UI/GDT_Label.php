<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A label is not field. It has no values. Only label. Label rendering is called on render functions.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
final class GDT_Label extends GDT
{
	use WithLabel;
	
	public function renderCLI() : string { return $this->displayLabel(); }
	public function renderXML() : string { return "<label>" . $this->displayLabel() . "</label>"; }
	public function renderHTML() : string { return "<label>" . $this->displayLabel() . "</label>"; }
	public function renderCard() : string { return $this->displayLabel(); }
	public function renderForm() : string { return $this->displayLabel(); }
	public function renderCell() : string { return $this->displayLabel(); }
	public function renderJSON() : string { return $this->displayLabel(); }
	
}
