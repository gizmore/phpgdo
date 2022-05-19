<?php
namespace GDO\CLI\Test;

use GDO\Tests\TestCase;
use GDO\Core\GDT_Expression;
use function PHPUnit\Framework\assertStringContainsString;

final class TestCLI extends TestCase
{
	public function testEcho()
	{
		$expression = GDT_Expression::fromLine("core.ekko 123");
		$response = $expression->execute();
		$content = $response->renderCLI();
		assertStringContainsString("123", $content, 'Test if core.ekko command works.');
		
		$expression = GDT_Expression::fromLine("echo 123");
		$response = $expression->execute();
		$content = $response->renderCLI();
		assertStringContainsString("123", $content, 'Test if echo command alias works.');
	}
	
}
