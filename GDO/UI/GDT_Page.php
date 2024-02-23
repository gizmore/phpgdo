<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDO_ExceptionFatal;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\ModuleLoader;
use GDO\Core\WithInstance;
use GDO\Session\GDO_Session;

/**
 * A website page object.
 * Adds 4 sidebars and 1 top response box.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 */
final class GDT_Page extends GDT
{

	use WithTitle;
	use WithInstance;
	use WithDescription;

	#############
	### Reset ###
	#############
	public string $html = '';

	##############
	### Render ###
	##############
	private GDT_Box $topResponse;

	###########
	### Top ###
	###########
	private GDT_Bar $topBar;
	private GDT_Bar $leftBar;
	private GDT_Bar $rightBar;

	###############
	### Navbars ###
	###############
	private GDT_Bar $bottomBar;

	/**
	 * Reset the global page object.
	 */
	public function reset(): static
	{
		unset($this->topBar);
		unset($this->leftBar);
		unset($this->rightBar);
		unset($this->bottomBar);
		unset($this->topResponse);
		return $this;
	}

	/**
	 * Render this website page in html mode.
	 * Include module scripts and sidebars for a full html page.
	 */
	public function renderHTML(): string
	{
		global $me;
		$loader = ModuleLoader::instance();
		$modules = $loader->getEnabledModules();
		$page = 'page_blank.php';
		if ($me && $me->isSidebarEnabled())
		{
			foreach ($modules as $module)
			{
				$module->onInitSidebar();
			}
			$page = 'page_html.php';
		}
		return GDT_Template::php('UI', $page, ['page' => $this]);
	}

    public function renderPDF(): string
    {
        return GDT_Template::php('UI', 'page_blank.php', ['page' => $this]);
    }

	public function topBar(): GDT_Bar
	{
		if (!isset($this->topBar))
		{
			$this->topBar = GDT_Bar::make('top')->horizontal();
		}
		return $this->topBar;
	}

	public function getSlot(string $slot): GDT_Container
	{
		switch ($slot)
		{
			case 'top':
				return $this->topResponse();
			case 'left':
				return $this->leftBar();
			case 'right':
				return $this->rightBar();
			case 'bottom':
				return $this->bottomBar();
			default:
				throw new GDO_ExceptionFatal('err_invalid_yield_slot', [html($slot)]);
		}
	}

	public function topResponse(): GDT_Box
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

	private function restoreSessionRedirectResponse(): void
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

	public function leftBar(): GDT_Bar
	{
		if (!isset($this->leftBar))
		{
			$this->leftBar = GDT_Bar::make('left')->vertical();
		}
		return $this->leftBar;
	}

	public function rightBar(): GDT_Bar
	{
		if (!isset($this->rightBar))
		{
			$this->rightBar = GDT_Bar::make('right')->vertical();
		}
		return $this->rightBar;
	}

	public function bottomBar(): GDT_Bar
	{
		if (!isset($this->bottomBar))
		{
			$this->bottomBar = GDT_Bar::make('bottom')->horizontal();
		}
		return $this->bottomBar;
	}

	public function html(string $html): self
	{
		$this->html = $html;
		return $this;
	}

}
