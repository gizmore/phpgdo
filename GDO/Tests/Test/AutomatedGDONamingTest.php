<?php
namespace GDO\Tests\Test;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use GDO\Core\GDT_Field;

/**
 * Test all GDO and Method fields to have a name.
 * 
 * @author gizmore
 */
final class AutomatedGDONamingTest extends AutomatedTestCase
{
	
	protected function getTestName(): string
	{
		return 'Test Named GDO columns and Method paramters';
	}
	
	public function testNaming(): void
	{
		$this->doAllGDO();
		$this->doAllMethods();
	}

	protected function runGDOTest(GDO $gdo): void
	{
		foreach ($gdo->gdoColumnsCache() as $gdt)
		{
			$this->tryGDT($gdt, get_class($gdo));
		}
	}
	
	protected function runGDTTest(GDT $gdt): void
	{
	}
	
	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		foreach ($mt->method->gdoParameterCache() as $gdt)
		{
			$this->tryGDT($gdt, get_class($mt->method));
		}
	}
	
	private function tryGDT(GDT $gdt, string $owner): void
	{
		if ($gdt instanceof GDT_Field)
		{
			assert($gdt->getName(), "Assert that $owner field {$gdt->gdoClassName()} has a name.");
		}
	}
	
}
