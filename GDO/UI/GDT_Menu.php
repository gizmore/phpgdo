<?php
namespace GDO\UI;

use GDO\Core\WithInput;
use GDO\Core\GDT_Template;

/**
 * A popup menu
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 */
class GDT_Menu extends GDT_Container
{
	use WithLabel;
	use WithInput;
	
	public bool $flex = true;
	public bool $flexWrap = true;
	public bool $flexShrink = true;
	
	public function getDefaultName() : string
	{
		return 'menu';
	}
	
	protected function __construct()
	{
		parent::__construct();
		$this->labelNone();
	}
	
	##############
	### Render ###
	##############
	public function renderForm() : string
	{
		return $this->renderMenu(true);
	}

	public function renderHTML() : string
	{
		return $this->renderMenu(false);
	}
	
	public function renderMenu(bool $isForm) : string
	{
		$this->setupHTML();
		return GDT_Template::php('UI', 'menu_html.php', [
			'field' => $this,
			'isForm' => $isForm, 
		]);
	}

}
