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
		$command = 'core.concat $(core.concat "--glue=, " b,c),a';
		$result = $this->cli($command);
		assertStringContainsString('b ca', $result, 'Check if complex concat works');
	}
	
	public function testDirectoryIndex()
	{
		$result = GDT_Expression::fromLine("core.directoryindex /GDO/")->execute();
		$result = $result->renderCLI();
		assertStringContainsString('GDO', $result, 'Check if directory index works');
	}
	
}
