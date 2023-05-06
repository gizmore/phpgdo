<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use GDO\UI\Color;
use GDO\UI\TextStyle;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotEquals;

/**
 * Test if all methods have a title and description.
 *
 * @version 7.0.3
 * @author gizmore
 */
final class SEOTest extends AutomatedTestCase
{

	public function testIfKeywordsAreThere()
	{
		assertNotEmpty(t('keywords'), 'Test if keywords are set.');
	}

	public function testAllMethods(): void
	{
		if (\gdo_test::instance()->seo)
		{
			$this->doAllMethods();
		}
		else
		{
			static::assertTrue(true);
		}
	}

	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		try
		{
			$plugged = [];

			$method = $mt->method;

			if ($method->isDebugging())
			{
				xdebug_break();
			}

			foreach ($method->gdoParameters() as $gdt)
			{
				if ($name = $gdt->getName())
				{
					if ($plugs = @$gdt->plugVars()[0])
					{
						foreach ($plugs as $name => $var)
						{
							$plugged[$name] = $var;
						}
					}
				}
			}

			$method->inputs($plugged);
			$method->onMethodInit();

			foreach ($method->gdoParameterCache() as $gdt)
			{
				if ($gdt->getName())
				{
					if ($plugs = @$gdt->plugVars()[0])
					{
						foreach ($plugs as $name => $var)
						{
							$plugged[$name] = $var;
						}
					}
				}
			}

			$method->appliedInputs($plugged);
			$title = $method->getMethodTitle();
			$descr = $method->getMethodDescription();
			assertNotEmpty($title, "Test if {$method->gdoClassName()} has a method title.");
			assertNotEmpty($descr, "Test if {$method->gdoClassName()} has a method description.");
		}
		catch (\Throwable $ex)
		{
			$this->error("%s %s: %s",
				Color::red('Failure'),
				TextStyle::bold($method->gdoClassName()),
				$ex->getMessage());
//			echo Debug::debugException($ex);
			throw $ex;
		}
	}

	protected function getTestName(): string
	{
		return 'SEO Test';
	}

	protected function runGDTTest(GDT $gdt): void {}

	protected function runGDOTest(GDO $gdo): void {}

}
