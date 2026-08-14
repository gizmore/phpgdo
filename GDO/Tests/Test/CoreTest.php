<?php
namespace GDO\Tests\Test;

use GDO\Core\Debug;
use GDO\Core\GDT_Expression;
use GDO\Core\Module_Core;
use GDO\Tests\TestCase;

/**
 * Core tests.
 *
 * @author gizmore
 * @version 7.0.4
 */
final class CoreTest extends TestCase
{

	public function testVersion()
	{
		$result = $this->cli('core.version');
		$version = Module_Core::GDO_REVISION;
		self::assertStringContainsString($version, $result, 'Test if version command works.');
	}

	public function testExpression()
	{
		$this->userGizmore();
		$result = GDT_Expression::fromLine('core.version')->execute()->renderCLI();
		self::assertStringContainsString(Module_Core::GDO_REVISION, $result, 'Check if an expression executes.');
	}

	public function testExceptionCount() : void
	{
		self::assertEquals(0, Debug::$EXCEPTIONS_UNHANDLED, "Assert that we have no more unhandled execptions!");
	}

}
