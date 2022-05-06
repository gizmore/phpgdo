<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

class GDT_Panel extends GDT
{
	use WithText;
	
	public function renderCLI()
	{
		return $this->renderText();
	}
	
	public function renderHTML()
	{
		return GDT_Template::php('UI', 'panel_html.php', ['field' => $this]);
	}

}
