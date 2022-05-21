<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_Hook;
use GDO\Core\Method;
use GDO\DB\Cache;
use GDO\Util\FileUtil;
use GDO\Tests\GDT_MethodTest;
use GDO\Core\ModuleLoader;
use GDO\Core\Module_Core;
use GDO\Core\Website;
use GDO\Core\GDT;
use GDO\DB\Database;
use GDO\Core\Application;

/**
 * Clears all client and server caches.
 * 
 * Does not save last url. Calls last url.
 * 
 * @TODO move Admin::ClearCache to Module_Core.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.1
 * @see GDO
 * @see Cache
 * @see Module_Core
 */
final class ClearCache extends Method
{
	use MethodAdmin;
	
	public function saveLastUrl() { return false; }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function execute()
	{
	    $this->clearCache();
		return Website::redirectMessage('msg_cache_flushed', null, Website::hrefBack());
	}
	
	public function clearCache()
	{
	    # Retrigger assets
	    $core = Module_Core::instance();
	    $assetVersion = $core->cfgAssetVersion();
	    $assetVersion->increase();
	    $core->saveConfigVar('asset_revision', $assetVersion->__toString());
	    # Flush memcached.
	    Cache::flush();
	    # Flush filecache
	    Cache::fileFlush();
	    # Flush GDO cache
	    Database::instance()->clearCache();
	    # Reset application state
	    Application::instance()->reset();
	    # Remove minified JS
	    FileUtil::removeDir(GDO_PATH . 'assets/');
	    # Clear module loader cache
	    ModuleLoader::instance()->reset();
	    # More caches
	    GDT_MethodTest::$USERS = [];
	    # Call hook
	    GDT_Hook::callWithIPC('ClearCache');
	}

}
