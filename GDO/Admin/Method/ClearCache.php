<?php
namespace GDO\Admin\Method;

use GDO\Core\GDO;
use GDO\Core\MethodAdmin;
use GDO\Core\GDT_Hook;
use GDO\Core\Method;
use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\Javascript\MinifyJS;
use GDO\Core\Module_Core;
use GDO\Core\Website;
use GDO\Core\ModuleLoader;

/**
 * Clears all client and server caches.
 * 
 * @TODO move to module core.
 * 
 * @author gizmore
 * @version 6.11.1
 * @since 6.0.1
 */
final class ClearCache extends Method
{
	use MethodAdmin;
	
	public function saveLastUrl() { return false; }
	
	public function getPermission() { return 'staff'; }
	
	public function execute()
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
	    Cache::fileFlush();
	    # Flush GDO cache
	    $this->flushAllGDOCaches();
	    # Remove minified JS
	    FileUtil::removeDir(MinifyJS::tempDirS());
	    # Call hook
	    GDT_Hook::callWithIPC('ClearCache');
	}
	
	private function flushAllGDOCaches()
	{
	    foreach (ModuleLoader::instance()->getEnabledModules() as $module)
	    {
	        if ($classes = $module->getClasses())
	        {
	            foreach ($classes as $classname)
	            {
	                /** @var $table GDO **/
	                $table = call_user_func([$classname, 'table']);
	                $table->clearCache();
	            }
	        }
	    }
	}

}
