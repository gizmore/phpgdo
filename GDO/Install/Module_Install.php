<?php
namespace GDO\Install;

use GDO\Core\GDO_Module;
use GDO\DB\Cache;

/**
 * 
 * @author gizmore
 *
 */
final class Module_Install extends GDO_Module
{
	public function isInstallable() : bool { return false; }
	
	public function onLoadLanguage() : void { $this->loadLanguage('lang/install'); }
	public function defaultEnabled() : bool { return false; }
	
	public function getTheme() : string
	{
		return 'install';
	}
	
	public function onInit() : void
	{
		Cache::flush();
		Cache::fileFlush();
	}
	
}
