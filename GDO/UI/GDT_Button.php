<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A simple button.
 * 
 * @see GDT_Submit
 * @see GDT_Link
 * @see GDT_IconButton
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
class GDT_Button extends GDT
{
	use WithText;
	use WithHREF;
	use WithAnchorRelation;
	
	##############
	### Render ###
	##############
	public function renderCell() : string
	{
	    if ($this->checkEnabled)
	    {
    	    $this->writable(call_user_func($this->checkEnabled, $this));
	    }
		return GDT_Template::php('UI', 'cell/button.php', ['field'=>$this, 'href'=>$this->gdoHREF()]);
	}
	
	public function renderForm() : string
	{
	    return $this->renderCell();
	}
	
	public function renderJSON() : string
	{
	    return sprintf('<a href="%s">%s</a>', $this->gdoHREF(), $this->htmlIcon());
	}

	#############
	### Proxy ###
	#############
	public function gdoHREF()
	{
	    if ($this->href)
	    {
	        return $this->href;
	    }
	    if ($this->gdo)
	    {
	    	$method_name = 'href_' . $this->name;
	    	if (method_exists($this->gdo, $method_name))
	    	{
				return call_user_func([$this->gdo, $method_name]);
	    	}
	    }
	}
	
	public function gdoLabel()
	{
		return call_user_func(
			[$this->gdo, 'display_'.$this->name]);
	}
	
	########################
	### Enabled callback ###
	########################
	public $checkEnabled;
	public function checkEnabled(callable $checkEnabled)
	{
	    $this->checkEnabled = call_user_func($checkEnabled);
	    return $this;
	}

}
