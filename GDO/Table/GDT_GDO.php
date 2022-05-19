<?php
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\WithGDO;

/**
 * Rendering wrapper for GDOs.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
final class GDT_GDO extends GDT
{
	use WithGDO;
	
	public function renderCard() : string { return $this->gdo->renderCard(); }
	public function renderCell() : string { return $this->gdo->renderCell(); }
	public function renderCLI() : string { return $this->gdo->renderCLI(); }
	public function renderChoice() : string { return $this->gdo->renderChoice(); }
	public function renderJSON() { return $this->gdo->renderJSON(); }
	public function renderXML() : string { return $this->gdo->renderXML(); }
	
}
