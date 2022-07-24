<?php
namespace GDO\Install;

use GDO\Core\GDO_Module;
use GDO\Admin\Method\ClearCache;

/**
 * Installer module.
 * Not installable itself.
 * Offers install theme.
 * Offers WWW Installer
 * Offers install utility.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.0
 */
final class Module_Install extends GDO_Module
{
	public function getTheme() : string	{ return 'install'; }
	public function isInstallable() : bool { return false; }
	public function defaultEnabled() : bool { return false; }
	
	public function onLoadLanguage() : void { $this->loadLanguage('lang/install'); }

	public function onInit()
	{
		ClearCache::make()->clearCache();
	}
	
}
