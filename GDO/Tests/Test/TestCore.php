<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\Core\GDT_Expression;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Core\Module_Core;

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
		$result = $this->cli("core.version");
		$version = Module_Core::GDO_REVISION;
		assertStringContainsString($version, $result, 'Test if version command works.');
	}
	
	public function testNestedExpression()
	{
		$result = GDT_Expression::fromLine()->execute();
		$result = $result->renderCLI();
		
		$command = 'core.concat $(core.concat "--glue=, " b,c),a';
		$result = $this->cli($command);
		assertStringContainsString('b ca', $result, 'Check if complex concat works');
	}
	
}
