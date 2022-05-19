<?php
namespace GDO\Install\Test;

use GDO\Tests\TestCase;
use GDO\Core\ModuleLoader;
use GDO\Install\Installer;
use GDO\DB\Database;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use GDO\DB\Cache;
use GDO\Core\GDO_Module;

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
		assertGreaterThanOrEqual(4, count($installed), 'Test if installer utility works.');
	}
	
	public function testWipeAll()
	{
		$result = Database::instance()->dropDatabase(GDO_DB_NAME);
		ModuleLoader::instance()->reset();
		
		GDO_Module::table()->clearCache();
		
		assertTrue(!!$result, 'Test if db can be dropped');
	
		$result = Database::instance()->createDatabase(GDO_DB_NAME);
		assertTrue(!!$result, 'Test if db can be re-created');
		
		$result = Database::instance()->useDatabase(GDO_DB_NAME);
		assertTrue(!!$result, 'Test if db can be re-used');
	}
	
}
