<?php
namespace GDO\Tests\Test;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use function PHPUnit\Framework\assertLessThan;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertTrue;
use GDO\Core\GDT_Response;

/**
 * Test all GDOv7 methods with plugvar fuzzing.
 *
 * @author gizmore
 */
final class AutomatedMethodTest extends AutomatedTestCase
{

	public function testAllMethods(): void
	{
		$this->doAllMethods();
	}
	
	protected function getTestName(): string
	{
		return "Method Execution";
	}
	
	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		$method = $mt->method;
		$mt->runAs($mt->method->plugUser());
		$result = $mt->execute(null, false);
		$this->assertNoCrash("Test if trivial method {$this->mome($method)} does not crash.");
		assertInstanceOf(GDT_Response::class, $result, "Test if method {$method->gdoClassName()} execution returns a GDT_Result.");
		assertTrue(!!$this->renderResult($result), "Test if method response renders all outputs without crash.");
	}

	/**
	 * Render a response in all 7 render modes.
	 */
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
	
	protected function runGDTTest(GDT $gdt): void
	{
	}
	
	protected function runGDOTest(GDO $gdo): void
	{
	}
	
}
