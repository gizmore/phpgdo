<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithGDO;
use GDO\Form\WithFormAttributes;

/**
 * A simple button with only a label.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 * @see GDT_Link
 * @see GDT_Submit
 */
class GDT_Button extends GDT
{
	use WithGDO;
	use WithHREF;
	use WithIcon;
	use WithLabel;
	use WithPHPJQuery;
	use WithFormAttributes;
	use WithAnchorRelation;
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
	    if ($this->checkEnabled)
	    {
    	    $this->writeable(call_user_func($this->checkEnabled, $this));
	    }
	    $args = ['field' => $this, 'href' => $this->gdoHREF()];
		return GDT_Template::php('UI', 'button_html.php', $args);
	}
	
// 	public function renderForm() : string
// 	{
// 	    return $this->renderCell();
// 	}
	
	public function renderJSON()
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
