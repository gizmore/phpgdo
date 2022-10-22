<?php
namespace GDO\Tests\Test;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Tests\GDT_MethodTest;
use function PHPUnit\Framework\assertNotEmpty;
use GDO\Tests\AutomatedTestCase;
use function PHPUnit\Framework\assertNotEquals;
use GDO\Account\Method\Settings;
use GDO\Forum\Method\BoardImage;

/**
 * Test if all methods have a title and description.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class SEOTest extends AutomatedTestCase
{
	public function testIfKeywordsAreThere()
	{
		assertNotEmpty(t('keywords'), 'Test if keywords are set.');
	}
	
	public function testAllMethods(): void
	{
		$this->doAllMethods();
	}
	
	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		$this->methodSEOTest($mt);
	}
	
	protected function getTestName(): string
	{
		return "SEO Test";
	}
	
	private function methodSEOTest(GDT_MethodTest $mt)
	{
		$plugged = [];
		
		$method = $mt->method;
		
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
		
// 		$method->inputs($plugged);
		$method->appliedInputs($plugged);
		$title = $method->getMethodTitle();
		$descr = $method->getMethodDescription();
		assertNotEmpty($title, "Test if {$method->gdoClassName()} has a method title.");
		assertNotEmpty($descr, "Test if {$method->gdoClassName()} has a method description.");
// 		if ($title === $descr)
// 		{
// 			$this->eWrror("%s: %s has no real method description.",
// 				Color::red('Warning'),
// 				TextStyle::bold(get_class($method)),
// 			);
// 		}
	}
	
	protected function runGDTTest(GDT $gdt): void
	{
	}
	
	protected function runGDOTest(GDO $gdo): void
	{
	}

}
