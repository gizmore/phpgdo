<?php
namespace GDO\Table\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertStringContainsString;

/**
 * Unit tests for the Table module. 
 * @author gizmore
 * @version 7.0.0
 */
final class TableTest extends TestCase
{
	public function testTableOrder()
	{
		$result = $this->cli("admin.users --ipp=1,--o=user_name");
		assertStringContainsString("~Gaston~", $result, 'Test if table is ordered in CLI mode.');
		
	}
	
	public function testTableWithArrayResult()
	{
		assertTrue(true); # @TODO implement some test for Module_Table
	}
	
}
