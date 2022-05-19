<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * Basic text panel.
 * 
 * @see GDT_Box
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_Panel extends GDT
{
	use WithText;
	
	public function renderCLI() : string
	{
		return $this->renderText();
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'panel_html.php', ['field' => $this]);
	}

}
