<?php
namespace GDO\Perf;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Enum;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;

/**
 * Performance statistics in footer.
 *
 * Config perf_bottom_bar to restrict footer to staff or all or none.
 * This module is part of the gdo7 core.
 *
 * @version 7.0.2
 * @since 5.3.0
 * @author gizmore
 * @see GDT_PerfBar
 */
final class Module_Perf extends GDO_Module
{

	public int $priority = 100;

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/perf');
	}

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_Enum::make('hook_sidebar')->enumValues('all', 'none', 'staff')->initial('staff'),
		];
	}

	/**
	 * Show performance footer.
	 */
	public function onInitSidebar(): void
	{
		if ($this->shouldShow())
		{
			GDT_Page::instance()->bottomBar()->addField(GDT_PerfBar::make('perf'));
		}
	}

	############
	### Hook ###
	############

	private function shouldShow(): bool
	{
		switch ($this->cfgBottomPermission())
		{
			case 'all':
				return true;
			case 'none':
				return false;
			case 'staff':
				return GDO_User::current()->hasPermission('staff');
		}
	}

	public function cfgBottomPermission(): string { return $this->getConfigValue('hook_sidebar'); }

}
