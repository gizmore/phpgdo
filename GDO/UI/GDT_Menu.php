<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\WithInput;

/**
 * A popup menu
 *
 * @version 7.0.1
 * @since 6.4.0
 * @author gizmore
 */
class GDT_Menu extends GDT_Container
{

	use WithLabel;
	use WithInput;

	public bool $flex = true;
	public bool $flexWrap = true;
	public bool $flexShrink = false;

	protected function __construct()
	{
		parent::__construct();
		$this->labelNone();
	}

	public function getDefaultName(): string
	{
		return 'menu';
	}

	##############
	### Render ###
	##############

	public function renderForm(): string
	{
		return $this->renderMenu(true);
	}

	public function renderMenu(bool $isForm): string
	{
		$this->setupHTML();
		return GDT_Template::php('UI', 'menu_html.php', [
			'field' => $this,
			'isForm' => $isForm,
		]);
	}

	public function renderHTML(): string
	{
		return $this->renderMenu(false);
	}

}
