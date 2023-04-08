<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\GDT_Hook;
use GDO\Core\Module_Core;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Tests\GDT_MethodTest;
use GDO\UI\GDT_Redirect;
use GDO\Util\FileUtil;

/**
 * Clears all client and server caches.
 *
 * Does not save last url. Calls last url.
 *
 * @version 7.0.3
 * @since 6.0.1
 * @author gizmore
 * @see GDO
 * @see Cache
 * @see Module_Core
 */
class ClearCache extends MethodForm
{

	use MethodAdmin;

	public function getCLITrigger(): string { return 'cc'; }

	public function getPermission(): ?string { return 'staff'; }

	public function isTrivial(): bool { return false; } # Clearing the cache during tests randomly is not a good idea.

	public function isSavingLastUrl(): bool { return false; }

	public function getMethodTitle(): string
	{
		return t('btn_clearcache');
	}

	public function createForm(GDT_Form $form): void
	{
		$form->addField(GDT_AntiCSRF::make());
		$form->actions()->addField(GDT_Submit::make());
	}

	public function execute(): GDT
	{
		$this->clearCache();
		return GDT_Redirect::make()->redirectMessage('msg_cache_flushed')->back();
	}

	public function clearCache(): void
	{
		$core = Module_Core::instance();
		# Retrigger assets (requires db)
		if (!Application::$INSTANCE->isInstall())
		{
			# needs a db set up.
			$assetVersion = $core->cfgAssetVersion();
			$assetVersion->increase();
			$core->saveConfigVar('asset_revision', $assetVersion->__toString());
			# needs a db set up :/
		}
		# DB
		Database::clearCache();
		# Flush memcached and filecache.
		Cache::flush();
		# Reset application state
		Application::$INSTANCE->reset();
		# Remove minified JS
		FileUtil::removeDir(GDO_PATH . 'assets/', false);
		# More caches
		GDT_MethodTest::$TEST_USERS = [];
		# Call hook
		GDT_Hook::clearCache();
		GDT_Hook::callWithIPC('ClearCache');
	}

}
