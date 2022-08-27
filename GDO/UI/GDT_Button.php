<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithGDO;
use GDO\Form\WithFormAttributes;
use GDO\Form\WithClickHandler;

/**
 * A simple button with only a label, href and icon.
 * 
 * - Can be marked as secondary.
 * - Can have a GDO to generate it's HREF.
 * 
 * @TODO: PHPJQuery can be used to attach some JS to buttons?
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 * @see GDT_Link
 * @see GDT_Submit
 * @see WithGDO
 */
class GDT_Button extends GDT
{
	use WithGDO;
	use WithHREF;
	use WithIcon;
	use WithLabel;
	use WithPHPJQuery;
	use WithClickHandler;
	use WithFormAttributes;
	use WithAnchorRelation;
	
	#################
	### Secondary ###
	#################
	public bool $secondary = false;
	public function secondary(bool $secondary=true) : self
	{
		$this->secondary = $secondary;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return $this->renderHTMLCell(false);
	}
	
	public function renderCell() : string
	{
		return $this->renderHTMLCell(true);
	}
	
	private function renderHTMLCell(bool $cell) : string
	{
		if (isset($this->checkEnabled))
		{
			$this->writeable(call_user_func($this->checkEnabled, $this));
		}
		$args = [
			'field' => $this,
			'href' => $this->gdoHREF(),
			'cell' => $cell,
		];
		return GDT_Template::php('UI', 'button_html.php', $args);
	}
	
	public function renderForm() : string
	{
	    return $this->renderHTML();
	}
	
	public function renderOrder() : string
	{
		return GDT::EMPTY_STRING;
	}
	
	public function renderJSON()
	{
	    return sprintf('<a href="%s">%s</a>', $this->gdoHREF(), $this->htmlIcon());
	}
	
	public function renderLabel() : string
	{
		if (!($label = $this->renderLabelText()))
		{
			$label = $this->htmlGDOHREF();
		}
		return $label;
	}

	#############
	### Proxy ###
	#############
	public function htmlGDOHREF() : string
	{
		if ($href = $this->gdoHREF())
		{
			return " href=\"{$href}\"";
		}
		return GDT::EMPTY_STRING;
	}
	
	public function gdoHREF()
	{
		if (isset($this->href))
	    {
	        return $this->href;
	    }
	    if (isset($this->gdo))
	    {
	    	$method_name = 'href_' . $this->name;
// 	    	if (method_exists($this->gdo, $method_name))
	    	{
				return call_user_func([$this->gdo, $method_name]);
	    	}
	    }
	}
	
// 	public function gdoLabel()
// 	{
// 		return call_user_func(
// 			[$this->gdo, 'display_'.$this->name]);
// 	}
	
	########################
	### Enabled callback ###
	########################
	/**
	 * Do a callback check for the GDO if button is enabled?
	 * @var callable
	 */
	public $checkEnabled;
	public function checkEnabled(callable $checkEnabled) : self
	{
	    $this->checkEnabled = $checkEnabled;
	    return $this;
	}

}
