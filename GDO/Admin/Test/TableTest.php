<?php

namespace GDO\Admin\Test;

class TableTest extends \GDO\Tests\TestCase
{

	public function testTableOrder()
	{
		$result = $this->cli("admin.users --ipp=1,--o=user_name");
		\PHPUnit\Framework\assertStringContainsString("~Gaston~", $result, 'Test if table is ordered in CLI mode.');
	}

}
