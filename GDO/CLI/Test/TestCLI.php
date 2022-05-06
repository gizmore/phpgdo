<?php
namespace GDO\CLI\Test;

use GDO\Tests\TestCase;
use GDO\CLI\Method\Ekko;

final class TestCLI extends TestCase
{
	public function testEcho()
	{
		Ekko::make()->execute()
	}
	
}
