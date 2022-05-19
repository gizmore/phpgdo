<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_Hook;
use GDO\Core\Method;
use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\Javascript\MinifyJS;
use GDO\Core\Module_Core;
use GDO\Core\Website;
use GDO\Core\GDT;
use GDO\DB\Database;

/**
 * Clears all client and server caches.
 * 
 * @TODO move AdminClearCache to Module_Core.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.1
 * @see Module_Core
 */
final class ClearCache extends Method
{
	use MethodAdmin;
	
	public function saveLastUrl() { return false; }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function execute() : GDT
	{
	    $this->clearCache();
		return Website::redirectMessage('msg_cache_flushed', null, Website::hrefBack());
	}
	
	public function clearCache()
	{
	    # Retrigger assets
	    $core = Module_Core::instance();
	    $assetVersion = $core->cfgAssetVersion() + 0.01; # client cache
	    $core->saveConfigVar('asset_revision', sprintf('%.02f', round($assetVersion, 2)));
	    # Flush memcached.
	    Cache::flush();
	    # Flush filecache
	    Cache::fileFlush();
	    # Flush GDO cache
	    Database::instance()->clearCache();
	    # Flush GDO cache
// 	    $this->flushAllGDOCaches();
	    # Remove minified JS
	    FileUtil::removeDir(MinifyJS::tempDirS());
	    # Call hook
	    GDT_Hook::callWithIPC('ClearCache');
	}
	
// 	private function flushAllGDOCaches()
// 	{
// 	    foreach (ModuleLoader::instance()->getEnabledModules() as $module)
// 	    {
// 	        if ($classes = $module->getClasses())
// 	        {
// 	            foreach ($classes as $classname)
// 	            {
// 	                /** @var $table GDO **/
// 	                $table = call_user_func([$classname, 'table']);
// 	                $table->clearCache();
// 	            }
// 	        }
// 	    }
// 	}

}
