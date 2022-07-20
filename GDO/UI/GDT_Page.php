<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithInstance;
use GDO\Core\ModuleLoader;
use GDO\Core\Application;
use GDO\Session\GDO_Session;

/**
 * A website page object.
 * Adds 4 sidebars and a top response box.
 * 
 * @TODO: Add $sidebar to opt-in for sidebar generation code.
 * 
 * @author gizmore
 * @version 7.0.0
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
	public function reset() : self
	{
		unset($this->topResponse);
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
		if (!Application::$INSTANCE->isInstall())
		{
			foreach (ModuleLoader::instance()->getEnabledModules() as $module)
			{
				$module->onIncludeScripts();
				$module->onInitSidebar();
			}
		}
		return GDT_Template::php('UI', 'page.php', ['page' => $this]);
	}
	
	############
	### Bars ###
	############
	private GDT_Box $topResponse;
	
	public function topResponse() : GDT_Box
	{
		if (!isset($this->topResponse))
		{
			$this->topResponse = GDT_Box::make()->vertical();
			if (class_exists('GDO\\Session\\GDO_Session', false))
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
