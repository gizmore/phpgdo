<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithGDO;
use GDO\Form\WithClickHandler;
use GDO\Form\WithFormAttributes;

/**
 * A simple button with only a label, href and icon.
 *
 * - Can be marked as secondary.
 * - Can have a GDO to generate it's HREF.
 *
 * @TODO: PHPJQuery can be used to attach some JS to buttons?
 *
 * @version 7.0.1
 * @since 6.1.0
 * @author gizmore
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
	/**
	 * Do a callback check for the GDO if button is enabled?
	 *
	 * @var callable
	 */
	public $checkEnabled;

	##############
	### Render ###
	##############

	public function secondary(bool $secondary = true): self
	{
		$this->secondary = $secondary;
		return $this;
	}

	public function renderTHead(): string
	{
		return GDT::EMPTY_STRING;
	}

	public function renderLabel(): string
	{
		if (!($label = $this->renderLabelText()))
		{
			$label = $this->htmlGDOHREF();
		}
		return $label;
	}	public function renderHTML(): string
	{
		return $this->renderHTMLCell(false);
	}

	public function htmlGDOHREF(): string
	{
		if ($href = $this->gdoHREF())
		{
			return " href=\"{$href}\"";
		}
		return GDT::EMPTY_STRING;
	}

	public function gdoHREF(): ?string
	{
		if (isset($this->href))
		{
			return $this->href;
		}
		if (isset($this->gdo))
		{
			$method_name = 'href_' . $this->name;
// 	    	if (method_exists($this->gdo, $method_name))
// 	    	{
			return call_user_func([
				$this->gdo, $method_name]);
// 	    	}
		}
		return null;
	}

	public function checkEnabled(callable $checkEnabled): self
	{
		$this->checkEnabled = $checkEnabled;
		return $this;
	}	public function renderCell(): string
	{
		return $this->renderHTMLCell(true);
	}



	private function renderHTMLCell(bool $cell): string
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


	public function renderForm(): string
	{
		return $this->renderHTML();
	}


	public function renderJSON()
	{
		return sprintf('<a href="%s">%s</a>', $this->gdoHREF(), $this->htmlIcon());
	}



	#############
	### Proxy ###
	#############




// 	public function gdoLabel()
// 	{
// 		return call_user_func(
// 			[$this->gdo, 'display_'.$this->name]);
// 	}

	########################
	### Enabled callback ###
	########################


}
