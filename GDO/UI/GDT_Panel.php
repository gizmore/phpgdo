<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithName;

/**
 * Basic text panel.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 * @see GDT_Box
 * @see GDT_Label
 */
class GDT_Panel extends GDT
{

	use WithName;
	use WithIcon;
	use WithText;
	use WithTitle;
	use WithPHPJQuery;

	protected function __construct()
	{
		parent::__construct();
		$this->addClass('gdt-panel');
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'panel_html.php', ['field' => $this]);
	}

	public function renderCLI(): string
	{
		return $this->renderText(); # . "\n";
	}

}
