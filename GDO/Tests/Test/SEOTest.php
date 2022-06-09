<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertNotEmpty;
use GDO\Core\Method;
use GDO\CLI\CLI;
use GDO\Core\Debug;

/**
 * Test if all methods have a title and description.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class SEOTest extends TestCase
{
	private int $methodsTested = 0;
	private int $methodsFailed = 0;
	private int $methodsAbstract = 0;
	private int $methodsNonTestable = 0;

	public function testIfKeywordsAreThere()
	{
		assertNotEmpty(t('keywords'), 'Test if keywords are set.');
	}
	
	public function testIfAllMethodsHavePageTitleAndDescription()
	{
		foreach (get_declared_classes() as $klass)
		{
			$parents = class_parents($klass);
			if (in_array('GDO\\Core\\Method', $parents, true))
			{
				$n = ++$this->methodsTested;
				/** @var $method \GDO\Core\Method **/
				$k = new \ReflectionClass($klass);
				if ($k->isAbstract())
				{
					$this->methodsAbstract++;
					continue;
				}
				$method = call_user_func([
					$klass,
					'make'
				], 'autorender_'.$n);
				if (!$method->isTrivial())
				{
					$this->methodsNonTestable++;
					continue;
				}
				try
				{
					$this->methodSEOTest($method);
					$this->message("%s: %s",
						CLI::green(CLI::bold("SUCCESS")),
						$method->gdoClassName());
				}
				catch (\Throwable $t)
				{
					$this->methodsFailed++;
					$this->error("%s: %s: %s",
						CLI::red(CLI::bold("FAILURE")),
						$method->gdoClassName(),
						$t->getMessage());
					Debug::exception_handler($t);
				}
			}
		}
	}
	
	private function methodSEOTest(Method $method)
	{
		$plugged = [];
		
		foreach ($method->gdoParameters() as $gdt)
		{
			if ($name = $gdt->getName())
			{
				if ($plugs = $gdt->plugVars())
				{
					$plugged[$name] = $plugs[0];
				}
			}
		}

		$method->inputs($plugged);
		$method->onInit();

		foreach ($method->gdoParameterCache() as $gdt)
		{
			if ($name = $gdt->getName())
			{
				if ($plugs = $gdt->plugVars())
				{
					$plugged[$name] = $plugs[0];
				}
			}
		}
		$method->inputs($plugged);

		
		assertNotEmpty($method->getMethodTitle(), "Test if {$method->gdoClassName()} has a method title.");
		assertNotEmpty($method->getMethodDescription(), "Test if {$method->gdoClassName()} has a method description.");
	}
	
}
