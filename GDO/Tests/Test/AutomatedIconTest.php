<?php
namespace GDO\Tests\Test;

use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use GDO\UI\GDT_IconUTF8;
use Throwable;

/**
 * Test if all default icons exist.
 * Tests all GDO/GDT and Methods automatically.
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 */
final class AutomatedIconTest extends AutomatedTestCase
{

	public function testUTF8Icons(): void
	{
		if (\gdo_test::instance()->icons)
		{
			$this->doAllGDT();
			$this->doAllMethods();
		}
		else
		{
			self::assertTrue(true);
		}
	}

	protected function getTestName(): string
	{
		return 'Automated Icon Test';
	}

	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		foreach ($mt->method->gdoParameterCache() as $gdt)
		{
			$this->runGDTTest($gdt);
		}
	}

	protected function runGDTTest(GDT $gdt): void
	{
		try
		{
// 			if ($gdt instanceof GDT_CreatedBy)
// 			{
// 				xdebug_break();
// 			}
// 			echo "GDT: {$gdt->gdoClassName()}\n";
// 			@ob_flush();
			$classname = get_class($gdt);
// 			if ($this->class_uses_trait($classname, WithIcon::class))
// 			{
			if (isset($gdt->icon))
			{
				assert(isset(GDT_IconUTF8::$MAP[$gdt->icon]), 'Test if icon ' . $gdt->icon . ' exists for ' . $classname);
			}
// 			}
		}
		catch (Throwable $ex)
		{
			echo "GDT: {$gdt->gdoClassName()}\n";
			echo Debug::debugException($ex);
			@ob_flush();
			throw $ex;
		}
	}

	protected function runGDOTest(GDO $gdo): void
	{
		foreach ($gdo->gdoColumnsCache() as $gdt)
		{
			$this->runGDTTest($gdt);
		}
	}

}
