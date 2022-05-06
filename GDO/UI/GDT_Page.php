<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;

/**
 * A website page object.
 * 
 * @author gizmore
 */
final class GDT_Page extends GDT
{
	use WithTitle;
	use WithFields;
	use WithDescription;
	
	public function renderHTML()
	{
		return GDT_Template::php('UI', 'page.php', ['page' => $this]);
	}
	
	public function renderCLI()
	{
		$back = '';
		foreach ($this->getFields() as $gdt)
		{
			$back .= $gdt->renderCLI();
		}
		return $back;
	}
	
}
