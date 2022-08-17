<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use function PHPUnit\Framework\assertTrue;

/**
 * Test if module default configs are valid.
 * 
 * @author gizmore
 */
final class AutomatedModuleConfigTest extends TestCase
{
	public function testModulesConfigDefaults() : void
	{
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			$this->singleModuleTest($module);
		}
	}
	
	private function singleModuleTest(GDO_Module $module)
	{
		foreach ($module->getConfig() as $gdt)
		{
			$this->gdtTest($gdt);
		}
	}
	
	private function gdtTest(GDT $gdt)
	{
		assertTrue($gdt->validate($gdt->getValue()), "Check if default value for {$gdt->getName()} is ok.");
	}
	
}
