<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\Core\GDO;
use GDO\Core\GDO_Exception;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\Method\Health;
use GDO\Date\Method\Timezone;
use GDO\Language\Method\SwitchLanguage;
use GDO\Tests\AutomatedTestCase;
use GDO\Tests\GDT_MethodTest;
use GDO\UI\TextStyle;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertLessThan;
use function PHPUnit\Framework\assertTrue;

/**
 * Test all GDOv7 methods with plugvar fuzzing.
 *
 * @author gizmore
 * @version 7.0.3
 */
final class AutomatedMethodTest extends AutomatedTestCase
{

	public function testAllMethods(): void
	{
		if (\gdo_test::instance()->methods)
		{
			$this->doAllMethods();
		}
		else
		{
			self::assertTrue(true);
		}
	}

	protected function getTestName(): string
	{
		return 'Method Execution';
	}

	/**
	 * @throws GDO_Exception
	 */
	protected function runMethodTest(GDT_MethodTest $mt): void
	{
		$method = $mt->method;
//		$this->message('Running command: ' . TextStyle::bold($method->getCLITrigger()));
		if ($method->isDebugging())
		{
			xdebug_break();
		}
		$mt->runAs($mt->method->plugUser());
		$result = $mt->execute();
		$this->assertNoCrash("Test if trivial method {$this->mome($method)} does not crash.");
		assertInstanceOf(GDT_Response::class, $result, "Test if method {$method->gdoClassName()} execution returns a GDT_Result.");
		assertTrue(!!$this->renderResult($result), 'Test if method response renders all outputs without crash.');
		$this->assertNoCrash("Test if trivial method rendering {$this->mome($method)} does not crash.");
	}

	/**
	 * Render a response in all 7 render modes.
	 */
	private function renderResult(GDT $response): bool
	{
		if (\gdo_test::instance()->rendering)
		{
			$response->renderMode(GDT::RENDER_BINARY);
			$response->renderMode(GDT::RENDER_CLI);
			$response->renderMode(GDT::RENDER_PDF);
			$response->renderMode(GDT::RENDER_XML);
			$response->renderMode(GDT::RENDER_JSON);
//			$response->renderMode(GDT::RENDER_GTK);
			$response->renderMode(GDT::RENDER_IRC);
			$response->renderMode(GDT::RENDER_WEBSITE);
		}
		return true;
	}

	protected function runGDTTest(GDT $gdt): void {}

	protected function runGDOTest(GDO $gdo): void {}

}
