<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * Basic text panel.
 * 
 * @TODO document difference between display methods and render methods.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 * @see GDT_Box
 */
class GDT_Panel extends GDT
{
	use WithIcon;
	use WithText;
	use WithTitle;
	use WithPHPJQuery;
	
	protected function __construct()
	{
		parent::__construct();
		$this->addClass('gdt-panel');
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'panel_html.php', ['field' => $this]);
	}
	
	public function renderCLI() : string
	{
		return $this->renderText() . "\n";
	}

}
