<?php
namespace GDO\Tests\Test;

use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use GDO\UI\WithIcon;
use GDO\UI\GDT_IconUTF8;
use GDO\Core\GDO;
use GDO\Core\GDT;

/**
 * Test if all icons exist.
 * Tests all GDO/GDT and Methods automatically.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class AutomatedIconTest extends AutomatedTestCase
{
	public function testUTF8Icons(): void
	{
		$this->doAllGDT();
		$this->doAllMethods();
	}
	
	protected function getTestName(): string
	{
		return 'Automated Icon Test';
	}
	
	protected function runGDTTest(GDT $gdt): void
	{
		$classname = get_class($gdt);
		if ($this->class_uses_trait($classname, WithIcon::class))
		{
			if (isset($gdt->icon))
			{
				assert(isset(GDT_IconUTF8::$MAP[$gdt->icon]), 'Test if icon exists for ' . $classname);
			}
		}
	}

	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		foreach ($mt->method->gdoParameterCache() as $gdt)
		{
			$this->tryClassname(get_class($gdt));
		}
	}
	
	protected function runGDOTest(GDO $gdo): void
	{
	}
	
}
