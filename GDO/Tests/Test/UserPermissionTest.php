<?php
namespace GDO\Tests\Test;

use GDO\User\GDO_User;
use GDO\Util\BCrypt;
use GDO\User\GDO_UserPermission;
use GDO\Tests\MethodTest;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertFalse;

/**
 * Generate a few users to work with.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.0
 */
final class UserPermissionTest extends TestCase
{
    public function testDefaultUsers()
    {
        echo "Creating 4 users for testing\n";
        # User 2 is gizmore
        $user = GDO_User::blank([
            'user_id' => '2',
            'user_name' => 'gizmore',
            'user_type' => 'member',
            'user_password' => BCrypt::create('11111111')->__toString(),
        ])->replace();
        GDO_UserPermission::table()->grant($user, 'admin');
        GDO_UserPermission::table()->grant($user, 'cronjob');
        GDO_UserPermission::table()->grant($user, 'staff');
        MethodTest::$USERS[] = $user;
        $user->changedPermissions();
        assertTrue($user->isAdmin(), "Test if admin permissions can be granted.");
        
        # User 3 is Peter
        $user = GDO_User::blank([
            'user_id' => '3',
            'user_name' => 'Peter',
            'user_type' => 'member',
            'user_password' => BCrypt::create('11111111')->__toString(),
        ])->replace();
        GDO_UserPermission::table()->grant($user, 'staff');
        MethodTest::$USERS[] = $user;
        $user->changedPermissions();
        assertFalse($user->isAdmin(), "Test if admin permissions are assigned correctly.");
        assertTrue($user->isStaff(), "Test if staff permissions are assigned correctly.");
        
        # User 4 is Monica
        $user = GDO_User::blank([
            'user_id' => '4',
            'user_name' => 'Monica',
            'user_type' => 'member',
            'user_password' => BCrypt::create('11111111')->__toString(),
        ])->replace();
        MethodTest::$USERS[] = $user;
        $user->changedPermissions();
        assertFalse($user->isAdmin(), "Test if admin permissions are assigned correctly.");
        assertFalse($user->isStaff(), "Test if staff permissions are assigned correctly.");
        assertFalse($user->isGuest(), 'Test if members are non guests.');
        assertTrue($user->isMember(), 'Test if members are members.');
        
        # User 5 is guest
        $user = GDO_User::blank([
            'user_id' => '5',
            'user_guest_name' => 'Gaston',
            'user_type' => 'guest',
        ])->replace();
        MethodTest::$USERS[] = $user;
        assertFalse($user->isAdmin(), "Test if admin permissions are assigned correctly.");
        assertFalse($user->isStaff(), "Test if staff permissions are assigned correctly.");
        assertTrue($user->isGuest(), 'Test if guests are guests.');
        assertFalse($user->isMember(), 'Test if guests are non members.');
        
        # User 6 is sven / staff
        $user = GDO_User::blank([
            'user_id' => '6',
            'user_name' => 'Sven',
            'user_type' => 'member',
        ])->replace();
        GDO_UserPermission::table()->grant($user, 'staff');
        MethodTest::$USERS[] = $user;
        $user->changedPermissions();
        assertFalse($user->isGhost(), "Test if staff is not ghost.");
        assertFalse($user->isGuest(), "Test if staff is not guest.");
        assertFalse($user->isAdmin(), "Test if staff is not admin.");
        assertTrue($user->isStaff(), "Test if staff has staff permissions assigned correctly.");
        assertTrue($user->isMember(), "Test if staff is a member.");
        
    }
    
}
