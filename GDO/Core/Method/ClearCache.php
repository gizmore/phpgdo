<?php
namespace GDO\Core\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_Hook;
use GDO\Core\Method;
use GDO\DB\Cache;
use GDO\Util\FileUtil;
use GDO\Tests\GDT_MethodTest;
use GDO\Core\Module_Core;
use GDO\DB\Database;
use GDO\Core\Application;
use GDO\UI\GDT_Redirect;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;

/**
 * Clears all client and server caches.
 * 
 * Does not save last url. Calls last url.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.1
 * @see GDO
 * @see Cache
 * @see Module_Core
 */
final class ClearCache extends MethodForm
{
	use MethodAdmin;
	
	public function isTrivial() : bool { return false; } # Clearing the cache during tests randomly is not a good idea.
	
	public function isSavingLastUrl() : bool { return false; }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function createForm(GDT_Form $form): void
	{
		$form->addField(GDT_AntiCSRF::make());
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function execute()
	{
	    $this->clearCache();
	    return GDT_Redirect::make()->redirectMessage('msg_cache_flushed')->back();
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
	    Application::$INSTANCE->reset();
	    # Remove minified JS
	    FileUtil::removeDir(GDO_PATH . 'assets/');
	    # More caches
	    GDT_MethodTest::$TEST_USERS = [];
	    # Call hook
	    GDT_Hook::clearCache();
	    GDT_Hook::callWithIPC('ClearCache');
	}

}
