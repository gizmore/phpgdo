<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertTrue;
use GDO\User\GDO_User;
use GDO\Core\GDO_DBException;
use GDO\User\GDO_Permission;

/**
 * Test foreign keys and related functionality.
 * 
 * @since 7.0.0
 */
final class CascadeTest extends TestCase
{
	public function testCascadeRestrict()
	{
		try
		{
			GDO_Permission::getById('1')->delete();
			assertTrue(false, 'Test if permissions cannot get deleted');
		}
		catch (GDO_DBException $ex)
		{
			assertTrue(GDO_Permission::getById('1')->isPersisted(), 'Test if language cannot be deleted.');
			assertTrue(GDO_User::getById('2')->isPersisted(), 'Test if user cannot be deleted.');
			assertTrue(GDO_User::table()->select()->first()->uncached()->exec()->fetchObject()->isPersisted(), 'Test if users are still there.');
		}
		
	}
	
}
