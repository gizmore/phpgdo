<?php
namespace GDO\Form;

use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;

/**
 * An HTML Hidden Form Fields.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_Hidden extends GDT_String
{
	public bool $writeable = false;

	public function isHidden() : bool { return true; }
	
	public function renderForm() : string
	{
		return GDT_Template::php('Form', 'hidden_html.php', ['field' => $this]);
	}
	
}
