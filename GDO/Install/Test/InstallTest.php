<?php
namespace GDO\Install\Test;

use GDO\Tests\TestCase;
use GDO\Core\ModuleLoader;
use GDO\Install\Installer;
use GDO\DB\Database;
use function PHPUnit\Framework\assertTrue;
use GDO\Admin\Method\ClearCache;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

/**
 * Install all available modules.
 * Wipe everything. reset cache.
 * The real testing begins.
 * 
 * @author gizmore
 */
final class InstallTest extends TestCase
{
	public function testInstallAllModules()
	{
		$loader = ModuleLoader::instance();
		$loader->loadModules(false, true, true);
		$modules = $loader->getInstallableModules();
		foreach ($modules as $module)
		{
			Installer::installModule($module);
		}
		
		$installed = $loader->getEnabledModules();
		assertGreaterThanOrEqual(7, count($installed), 'Test if installer utility works.');
	}
	
	public function testWipeAll()
	{
		echo "Clearing all caches deeply!\n";
		ClearCache::make()->clearCache();
		
		$result = Database::instance()->dropDatabase(GDO_DB_NAME);
		assertTrue(!!$result, 'Test if db can be dropped');
	
		$result = Database::instance()->createDatabase(GDO_DB_NAME);
		assertTrue(!!$result, 'Test if db can be re-created');
		
		$result = Database::instance()->useDatabase(GDO_DB_NAME);
		assertTrue(!!$result, 'Test if db can be re-used');
	}
	
}
