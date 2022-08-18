<?php
namespace GDO\Tests\Test;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use function PHPUnit\Framework\assertLessThan;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertTrue;

/**
 * Test all GDOv7 methods with plugvar fuzzing.
 *
 * @author gizmore
 * @see Permutations
 */
final class AutomatedMethodTest extends AutomatedTestCase
{

	public function testAllMethods(): void
	{
		$this->automatedMethods();
	}
	
	protected function getTestName(): string
	{
		return "Method Execution";
	}
	
	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		$method = $mt->method;
		$result = $mt->execute();
		assertLessThan(500, Application::$RESPONSE_CODE,
			"Test if trivial method {$this->mome($method)} does not crash.");
		assertInstanceOf(GDT::class, $result, "Test if method {$method->gdoClassName()} execution returns a GDT.");
		assertTrue($this->renderResult($result), "Test if method response renders all outputs without crash.");
	}

	private function renderResult(GDT $response): bool
	{
		$response->renderMode(GDT::RENDER_BINARY);
		$response->renderMode(GDT::RENDER_CLI);
		$response->renderMode(GDT::RENDER_PDF);
		$response->renderMode(GDT::RENDER_XML);
		$response->renderMode(GDT::RENDER_JSON);
		$response->renderMode(GDT::RENDER_GTK);
		$response->renderMode(GDT::RENDER_WEBSITE);
		return true;
	}
	
}
