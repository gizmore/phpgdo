<?php
namespace GDO\Tests\Test;

use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

/**
 * Test if module default configs and settings are valid.
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
final class AutomatedModuleConfigTest extends TestCase
{

	public function testModulesConfigDefaults(): void
	{
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			$this->singleModuleTest($module);
		}
	}

	private function singleModuleTest(GDO_Module $module)
	{
		foreach ($module->getConfigCache() as $gdt)
		{
			assertTrue($gdt->validate($gdt->getValue()), "Check if default config value for {$gdt->getName()} in module {$module->getName()} is ok.");
		}
		foreach ($module->getSettingsCache() as $gdt)
		{
			assertTrue($gdt->validate($gdt->getValue()), "Check if default setting value for {$gdt->getName()} in module {$module->getName()} is ok.");
		}
	}

}
