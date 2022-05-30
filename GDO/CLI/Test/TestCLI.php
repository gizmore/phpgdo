<?php
namespace GDO\CLI\Test;

use GDO\Tests\TestCase;
use GDO\Core\GDT_Expression;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertEquals;

final class TestCLI extends TestCase
{
	public function testMostBasicExpressions()
	{
		$exp = GDT_Expression::fromLine("cli.concat a;b");
		$gdt = $exp->execute();
		$res = $gdt->renderCLI();
		assertEquals('ab', $res, 'Test if \'concat a;b\' works');
	}
	
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
	
	public function testNestedConcat()
	{
		$result = $this->cli("concat --glue=,, ,a,b,(concat c,d),e");
		assertEquals("a, b, c, d, e", $result, 'Test if nested concat with a weird glue works.');
	}
	
}
