<?php

namespace GDO\Admin\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertStringContainsString;

class TableTest extends TestCase
{

	public function testTableOrder()
	{
		$result = $this->cli('admin.users --ipp=1,--o=user_name');
		assertStringContainsString('~Gaston~', $result, 'Test if table is ordered in CLI mode.');
	}

}
