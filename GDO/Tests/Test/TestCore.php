<?php
namespace GDO\Tests\Test;

use GDO\Core\Debug;
use GDO\Core\GDT_Expression;
use GDO\Core\Module_Core;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertStringContainsString;

/**
 * Core tests.
 *
 * - Test Expression parser
 *
 * @author gizmore
 */
final class TestCore extends TestCase
{

	public function testVersion()
	{
		$result = $this->cli('core.version');
		$version = Module_Core::GDO_REVISION;
		assertStringContainsString($version, $result, 'Test if version command works.');
	}

	public function testNestedExpression()
	{
		$command = 'core.concat $(core.concat "--glue=, " b,c),a';
		$result = $this->cli($command);
		assertStringContainsString('b ca', $result, 'Check if complex concat works');
	}

	public function testDirectoryIndex()
	{
		$result = GDT_Expression::fromLine('core.directoryindex /GDO/')->execute();
		$result = $result->renderCLI();
		assertStringContainsString('Net', $result, 'Check if directory index works');
	}

	public function testExceptionCount() : void
	{
		self::assertEquals(0, Debug::$EXCEPTIONS_UNHANDLED, "Assert that we have no more unhandled execptions!");
	}

}
