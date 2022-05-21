<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
use GDO\Core\WithInstance;
use GDO\Core\ModuleLoader;

/**
 * A website page object.
 * Offers 4 sidebars and a top response box.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
final class GDT_Page extends GDT
{
	use WithHTML;
	use WithTitle;
	use WithFields;
	use WithInstance;
	use WithDescription;
	
	#############
	### Reset ###
	#############
	/**
	 * Reset the global page object.
	 */
	public function reset() : self
	{
		unset($this->topBox);
		unset($this->topBar);
		unset($this->leftBar);
		unset($this->rightBar);
		unset($this->bottomBar);
		return $this;
	}
	
	##############
	### Render ###
	##############
	/**
	 * Render this page.
	 * Include module scripts for html page.
	 */
	public function renderHTML() : string
	{
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			$module->onIncludeScripts();
		}
		return GDT_Template::php('UI', 'page.php', ['page' => $this]);
	}
	
// 	public function renderCLI() : string
// 	{
// 		$back = '';
// 		foreach ($this->getFields() as $gdt)
// 		{
// 			$back .= $gdt->renderCLI();
// 		}
// 		return $back;
// 	}
	
	############
	### Bars ###
	############
	public GDT_Box $topBox;
	
	public function topResponse() : GDT_Box
	{
		if (!isset($this->topBox))
		{
			$this->topBox = GDT_Box::make()->horizontal();
		}
		return $this->topBox;
	}
	
	public GDT_Bar $topBar;
	public GDT_Bar $leftBar;
	public GDT_Bar $rightBar;
	public GDT_Bar $bottomBar;
	
	public function topBar() : GDT_Bar
	{
		if (!isset($this->topBar))
		{
			$this->topBar = GDT_Bar::make()->horizontal();
		}
		return $this->topBar;
	}
	
	public function leftBar() : GDT_Bar
	{
		if (!isset($this->leftBar))
		{
			$this->leftBar = GDT_Bar::make()->vertical();
		}
		return $this->leftBar;
	}
	
	public function rightBar() : GDT_Bar
	{
		if (!isset($this->rightBar))
		{
			$this->rightBar = GDT_Bar::make()->vertical();
		}
		return $this->rightBar;
	}
	
	public function bottomBar() : GDT_Bar
	{
		if (!isset($this->bottomBar))
		{
			$this->bottomBar = GDT_Bar::make()->horizontal();
		}
		return $this->bottomBar;
	}
	
}
