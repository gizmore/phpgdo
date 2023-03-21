<?php
namespace GDO\Install\Test;

use GDO\Core\ModuleLoader;
use GDO\Install\Installer;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertTrue;

/**
 * Install all available modules.
 * Wipe everything. reset cache.
 * The real testing begins afterwards.
 *
 * @version 7.0.2
 * @since 6.10.0
 * @author gizmore
 */
final class InstallAllModulesTest extends TestCase
{

	public function testInstallAllModules()
	{
		$loader = ModuleLoader::instance();
		$modules = $loader->loadModules(false, true, true);
		Installer::installModules($modules);
		$this->assertOK('Check if all modules can be installed.');
		$installed = $loader->getEnabledModules();
		assertGreaterThanOrEqual(5, count($installed), 'Test if installer utility works.');
	}

// 	public function testWipeAllModules()
// 	{
// 		echo "Clearing all caches again, deeply!\n";
// 		ClearCache::make()->clearCache();

// 		echo "Wiping database!\n";

// 		$db = Database::instance();
// 		$db->closeLink();
// 		$db->dropDatabase(GDO_DB_NAME);
// 		assertTrue(true, 'Test if db can be dropped');

// 		$db->createDatabase(GDO_DB_NAME);
// 		assertTrue(true, 'Test if db can be re-created');

// 		$db->useDatabase(GDO_DB_NAME);
// 		assertTrue(true, 'Test if db can be re-used');
// 	}

}
