<?php
namespace GDO\Install;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Template;
use GDO\Core\Method\ClearCache;

/**
 * Installer module.
 * Not installable itself.
 * Offers install theme.
 * Offers WWW Installer
 * Offers install utility.
 *
 * @version 7.0.2
 * @since 3.0.0
 * @author gizmore
 */
final class Module_Install extends GDO_Module
{

	public function getTheme(): ?string
	{
		return 'install';
	}

	public function isInstallable(): bool
	{
		return false;
	}

	public function defaultEnabled(): string
	{
		return false;
	}

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/install');
	}

	/**
	 * Forcefully register install theme.
	 */
	public function onModuleInit(): void
	{
		$path = $this->filePath('thm/install/');
		GDT_Template::registerTheme('install', $path);
		ClearCache::make()->clearCache();
	}

}
