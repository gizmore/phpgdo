<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\Core\GDO_DBException;
use GDO\Tests\TestCase;
use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertTrue;

/**
 * Test foreign keys and related functionality.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @see GDO_UserPermission
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
			$perm = GDO_Permission::table()->select()->where('perm_id=1')->uncached()->first()->exec()->fetchObject();
			assertIsObject($perm, 'Test if permissions cannot be deleted.');
		}
		catch (GDO_DBException $ex)
		{
			assertTrue(GDO_Permission::getById('1')->isPersisted(), 'Test if language cannot be deleted.');
			assertTrue(GDO_User::getById('2')->isPersisted(), 'Test if user cannot be deleted.');
			assertTrue(GDO_User::table()->select()->first()->uncached()->exec()->fetchObject()->isPersisted(), 'Test if users are still there.');
		}
	}

}
