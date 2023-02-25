<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertTrue;
use GDO\User\GDO_User;
use GDO\Core\GDO_DBException;
use GDO\User\GDO_Permission;
use function PHPUnit\Framework\assertIsObject;

/**
 * Test foreign keys and related functionality.
 * 
 * @version 7.0.2
 * @since 7.0.0
 */
final class CascadeTest extends TestCase
{
	/**
	 * Try to delete a permission that is in use.
	 */
	public function testCascadeRestrict()
	{
		try
		{
			GDO_Permission::getById('1')->delete();
			assertIsObject(GDO_Permission::findById('1'), 'Test if permissions cannot be deleted.');
		}
		catch (GDO_DBException $ex)
		{
			assertTrue(GDO_Permission::getById('1')->isPersisted(), 'Test if language cannot be deleted.');
			assertTrue(GDO_User::getById('2')->isPersisted(), 'Test if user cannot be deleted.');
			assertTrue(GDO_User::table()->select()->first()->uncached()->exec()->fetchObject()->isPersisted(), 'Test if users are still there.');
		}
		
	}
	
}
