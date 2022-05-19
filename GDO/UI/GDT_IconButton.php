<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;

/**
 * Like a GDT_Button but with an additional icon.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.2
 */
class GDT_IconButton extends GDT_Button
{
	use WithIcon;
	
	public function defaultLabel() : self { return $this->noLabel(); }
	
	public function renderHTML() : string
	{
	    return GDT_Template::php('UI', 'iconbutton_html.php', [
	        'field' => $this,
	        'href' => $this->gdoHREF()]);
	}
	
	public function htmlDisabled()
	{
		if ($this->isWritable() && $this->href)
		{
			return '';
		}
		return ' disabled="disabled"';
	}
	
}
