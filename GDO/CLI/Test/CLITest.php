<?php
namespace GDO\CLI\Test;

use GDO\Tests\TestCase;
use GDO\Core\GDT_Expression;
use GDO\Core\Expression\Parser;
use GDO\Util\PP;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertFalse;

final class CLITest extends TestCase
{
	public function testBasicExpressions()
	{
		$sep = Parser::ARG_SEPARATOR;
		$exp = GDT_Expression::fromLine("cli.concat a{$sep}b");
		$gdt = $exp->execute();
		$res = $gdt->renderCLI();
		assertEquals('ab', $res, 'Test if concat a,b works');
	}
	
	public function testEcho()
	{
		$expression = GDT_Expression::fromLine("cli.ekko 123");
		$response = $expression->execute();
		$content = $response->renderCLI();
		assertStringContainsString("123", $content, 'Test if core.ekko command works.');
		
		$expression = GDT_Expression::fromLine("echo 123");
		$response = $expression->execute();
		$content = $response->renderCLI();
		assertEquals("123", $content, 'Test if echo command alias works.');
	}
	
	public function testNestedConcat()
	{
		$result = $this->cli("cli.concat --glue=,, ,a,b,$(cli.concat c,d),e");
		assertEquals("a, b, cd, e", $result, 'Test if nested concat with a weird ,, glue works.');
	}

	public function testPP()
	{
		if (ini_get('allow_url_fopen'))
		{
			$empty = PP::init()->processString("<?php\n#PP#start#\n");
			assertEmpty(trim($empty), 'Test if #PP#start works without end.');
		}
		else
		{
			assertFalse((bool)ini_get('allow_url_fopen'), 'Warning removal urlfopen.');
		}
	}
	
}
