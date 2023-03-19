<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Simple HTML heading tag, like <h1>
 * Has level 1-5.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.7.0
 */
final class GDT_Headline extends GDT
{
    use WithText;
	
    ###############
    ### H-Level ###
    ###############
	public int $level = 1;
	public function level(int $level): static
	{
		$this->level = $level;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return sprintf('<h%d class="gdt-headline">%s</h%1$d>', $this->level, $this->renderText());
	}
	
}
