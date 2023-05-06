<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use GDO\UI\GDT_IconUTF8;
use Throwable;

/**
 * Test if all default icons exist.
 * Test all GDO/GDT and Methods automatically.
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

	protected function runGDTTest(GDT $gdt): void
	{
		try
		{
			if (Application::instance()->isUnitTestVerbose())
			{
				$this->message('Trying icons for GDT %s...', $gdt->gdoClassName());
			}
			$classname = get_class($gdt);
			if (isset($gdt->icon))
			{
				assert(isset(GDT_IconUTF8::$MAP[$gdt->icon]), 'Test if icon ' . $gdt->icon . ' exists for ' . $classname);
			}
		}
		catch (Throwable $ex)
		{
			Debug::debugException($ex);
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
