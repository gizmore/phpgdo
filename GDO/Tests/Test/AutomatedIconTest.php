<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Field;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use GDO\UI\GDT_IconUTF8;
use Throwable;

/**
 * Test if all default icons exist.
 * Test all GDO/GDT and Methods automatically.
 * Test if every GDT_Field has an icon.
 *
 * @version 7.0.3
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

	/**
	 * @throws Throwable
	 */
	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		$params = $mt->method->gdoParameterCache();
		if (count($params))
		{
			foreach ($params as $gdt)
			{
				$this->runGDTTest($gdt);
			}
		}
		else
		{
			$this->automatedPassed++;
		}
	}

	/**
	 * @throws Throwable
	 */
	protected function runGDTTest(GDT $gdt): void
	{
		try
		{
			$classname = get_class($gdt);
			if (isset($gdt->icon))
			{
				assert(isset(GDT_IconUTF8::$MAP[$gdt->icon]), 'Test if icon ' . $gdt->icon . ' exists for ' . $classname);
				assert(GDT_IconUTF8::$MAP[$gdt->icon] !== '', 'Test if icon ' . $gdt->icon . ' does really exist for ' . $classname);
			}
			elseif (!$gdt->isHidden())
			{
				self::assertNotTrue($gdt instanceof GDT_Field, "Test if {$classname} is not a GDT_Field, as it has no icon!");
			}
		}
		catch (Throwable $ex)
		{
			echo "GDT: {$gdt->gdoClassName()}\n";
			echo Debug::debugException($ex);
			if (ob_get_level())
			{
				ob_flush();
			}
			throw $ex;
		}
	}

	/**
	 * @throws Throwable
	 */
	protected function runGDOTest(GDO $gdo): void
	{
		foreach ($gdo->gdoColumnsCache() as $gdt)
		{
			$this->runGDTTest($gdt);
		}
	}

}
