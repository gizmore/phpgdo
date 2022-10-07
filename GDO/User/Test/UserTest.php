<?php
namespace GDO\User\Test;

use GDO\User\GDO_User;
use GDO\Core\Module_Core;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertEquals;
use GDO\Tests\GDT_MethodTest;
use GDO\User\Method\Profile;
use function PHPUnit\Framework\assertCount;
use GDO\Core\GDT;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

final class UserTest extends TestCase
{
    public function testSystemUser()
    {
        $u1 = Module_Core::instance()->cfgSystemUser();
        $u2 = GDO_User::system();
        assertTrue($u1 === $u2, 'Test single identity cache with system user.');

        $id = Module_Core::instance()->cfgSystemUserID();
        assertEquals($id, $u1->getID(), 'Test single identity cache with config system user.');
    }
    
    public function testGuestCreation()
    {
        $user = GDO_User::blank([
            'user_name' => 'Wolfgang',
            'user_type' => 'guest',
        ])->insert();
        assertFalse($user->isMember(), 'Test if guests are non members.');
    }
    
    public function testProfileGender()
    {
    	$user = GDO_User::current();
    	$user->saveSettingVar('User', 'gender', 'male');
    	$me = GDT_MethodTest::make()->method(Profile::make())->inputs(['for' => 'gizmore']);
    	$result = $me->execute();
    	$html = $result->renderMode(GDT::RENDER_WEBSITE);
    	assertGreaterThanOrEqual(1, substr_count($html, 'male'), 'Test if gizmore can change to male');
    }
    
}
