<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithInstance;
use GDO\Core\ModuleLoader;
use GDO\Session\GDO_Session;

/**
 * A website page object.
 * Adds 4 sidebars and 1 top response box.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
final class GDT_Page extends GDT
{
	use WithHTML;
	use WithTitle;
	use WithInstance;
	use WithDescription;
	
	#############
	### Reset ###
	#############
	/**
	 * Reset the global page object.
	 */
	public function reset(bool $removeInput=false) : self
	{
		unset($this->topBar);
		unset($this->leftBar);
		unset($this->rightBar);
		unset($this->bottomBar);
		unset($this->topResponse);
		return $this;
	}
	
	##############
	### Render ###
	##############
	/**
	 * Render this website page in html mode.
	 * Include module scripts and sidebars for a full html page.
	 */
	public function renderHTML() : string
	{
		global $me;
		$loader = ModuleLoader::instance();
		foreach ($loader->getEnabledModules() as $module)
		{
			$module->onIncludeScripts();
		}
		if ($me->isSidebarEnabled())
		{
			foreach ($loader->getEnabledModules() as $module)
			{
				$module->onInitSidebar();
			}
			return GDT_Template::php('UI', 'page_html.php', ['page' => $this]);
		}
		return GDT_Template::php('UI', 'page_blank.php', ['page' => $this]);
	}
	
	###########
	### Top ###
	###########
	private GDT_Box $topResponse;
	
	public function topResponse() : GDT_Box
	{
		if (!isset($this->topResponse))
		{
			$this->topResponse = GDT_Box::make()->vertical();
			if (module_enabled('Session'))
			{
				$this->restoreSessionRedirectResponse();
			}
		}
		return $this->topResponse;
	}
	
	private function restoreSessionRedirectResponse()
	{
		if ($error = GDO_Session::get('redirect_error'))
		{
			$this->topResponse->addField(GDT_Error::make()->textRaw($error));
			GDO_Session::remove('redirect_error');
		}
		if ($message = GDO_Session::get('redirect_message'))
		{
			$this->topResponse->addField(GDT_Success::make()->textRaw($message));
			GDO_Session::remove('redirect_message');
		}
	}
	
	###############
	### Navbars ###
	###############
	private GDT_Bar $topBar;
	private GDT_Bar $leftBar;
	private GDT_Bar $rightBar;
	private GDT_Bar $bottomBar;
	
	public function topBar() : GDT_Bar
	{
		if (!isset($this->topBar))
		{
			$this->topBar = GDT_Bar::make('top')->horizontal();
		}
		return $this->topBar;
	}
	
	public function leftBar() : GDT_Bar
	{
		if (!isset($this->leftBar))
		{
			$this->leftBar = GDT_Bar::make('left')->vertical();
		}
		return $this->leftBar;
	}
	
	public function rightBar() : GDT_Bar
	{
		if (!isset($this->rightBar))
		{
			$this->rightBar = GDT_Bar::make('right')->vertical();
		}
		return $this->rightBar;
	}
	
	public function bottomBar() : GDT_Bar
	{
		if (!isset($this->bottomBar))
		{
			$this->bottomBar = GDT_Bar::make('bottom')->horizontal();
		}
		return $this->bottomBar;
	}
	
}
