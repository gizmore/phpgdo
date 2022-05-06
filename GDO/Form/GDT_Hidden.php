<?php
namespace GDO\Form;

use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;

final class GDT_Hidden extends GDT_String
{
	public function renderHTML() : string
	{
		return GDT_Template::php('Form', 'hidden_html.php', ['field' => $this]);
	}
	
}
