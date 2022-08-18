<?php
namespace GDO\Tests\Test;

use GDO\Tests\GDT_MethodTest;
use function PHPUnit\Framework\assertNotEmpty;
use GDO\Tests\AutomatedTestCase;
use function PHPUnit\Framework\assertNotEquals;

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
		$this->automatedMethods();
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
		$method->onInit();

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
		
		$method->inputs($plugged);
		$title = $method->getMethodTitle();
		$descr = $method->getMethodDescription();
		assertNotEmpty($title, "Test if {$method->gdoClassName()} has a method title.");
		assertNotEmpty($descr, "Test if {$method->gdoClassName()} has a method description.");
// 		assertNotEquals($title, $descr, "Test if {$method->gdoClassName()} title differs from description.");
	}

	
}
