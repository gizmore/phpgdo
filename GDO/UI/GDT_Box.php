<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;

class GDT_Box extends GDT_Bar
{
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'box_html.php', ['field' => $this]);
	}
	
}
