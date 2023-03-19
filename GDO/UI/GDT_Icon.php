<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Just a single icon.
 * CLI always renders UTF8 icon set.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see WithIcon for rendering
 */
class GDT_Icon extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithPHPJQuery;

	/**
	 * When an icon provider is loaded, it changes the $iconProvider.
	 * @var callable
	 */
	public static $iconProvider = [GDT_IconUTF8::class, 'iconS'];
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return (string) $this->htmlIcon();
	}
	
	public function renderCLI() : string
	{
		return $this->cliIcon();
	}
	
	public function renderJSON()
	{
		if (isset($this->icon))
		{
			GDT_IconUTF8::iconS($this->icon, '', null);
		}
	}
	
	public function var(string $var = null): static
	{
	    parent::var($var);
	    return $this->icon($var);
	}
	
	public function value($value): static
	{
	    parent::value($value);
	    return $this->icon($value);
	}
	
}
