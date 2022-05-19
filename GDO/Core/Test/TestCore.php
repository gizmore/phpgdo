<?php
namespace GDO\Core\Test;

use GDO\Tests\TestCase;
use GDO\Core\GDT_Expression;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Core\Module_Core;

/**
 * Core tests without any users etc.
 * - Test Expression parser
 * 
 * @author gizmore
 */
final class TestCore extends TestCase
{
	public function testVersion()
	{
		$result = GDT_Expression::fromLine('version')->execute();
		$result = $result->renderCLI();
		assertStringContainsString(Module_Core::GDO_REVISION, $result, 'Check if version can be printed');
	}
	
	public function testNestedExpression()
	{
		$result = GDT_Expression::fromLine('core.concat $(core.concat "--glue= " b c) a')->execute();
		$result = $result->renderCLI();
		assertStringContainsString('b ca', $result, 'Check if concat works');
	}
	
}
