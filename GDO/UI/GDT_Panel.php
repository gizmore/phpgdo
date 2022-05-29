<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithParent;

/**
 * Basic text panel.
 * 
 * @TODO document difference between display methods and render methods.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 * @see GDT_Box
 */
class GDT_Panel extends GDT
{
	use WithText;
	use WithPHPJQuery;
	
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'panel_html.php', ['field' => $this]);
	}

}
