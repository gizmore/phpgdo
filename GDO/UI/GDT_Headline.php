<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Simple HTML heading tag, like <h1>
 * Has level 1-5
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.7.0
 */
final class GDT_Headline extends GDT
{
    use WithText;
	
	public int $level = 1;
	public function level(int $level) : self { $this->level = $level; return $this; }
	
	public function renderHTML() : string
	{
		return sprintf('<h%d>%s</h%1$d>', $this->level, $this->renderText());
	}
	
// 	public function renderCell() : string { return $this->hasText() ? sprintf('<h%1$d class="gdt-headline">%2$s</h%1$d>', $this->level, $this->renderText()) : ''; }
// 	public function renderForm() : string { return $this->renderCell(); }
// 	public function renderCard() : string { return $this->renderCell(); }
// 	public function renderJSON() { return ['headline' => $this->renderText(), 'level' => $this->level]; }
	
}
