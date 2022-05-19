<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Just a single icon.
 * 
 * @see WithIcon
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_Icon extends GDT
{
	use WithIcon;
	use WithPHPJQuery;

	/**
	 * When an icon provider is loaded, it changes the $iconProvider.
	 * @var callable
	 */
	public static $iconProvider = [GDT_IconUTF8::class, 'iconS'];
	
	##############
	### Render ###
	##############
	public function renderCell() : string { return $this->htmlIcon(); }
	public function renderCLI() : string { return $this->icon; }
	public function renderJSON() {}
	
	public function var(string $var = null) : self
	{
	    parent::var($var);
	    return $this->icon($var);
	}
	
	public function value($value) : self
	{
	    parent::value($value);
	    return $this->icon($value);
	}
	
}
