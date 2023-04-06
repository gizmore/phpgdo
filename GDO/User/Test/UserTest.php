<?php
namespace GDO\User\Test;

use GDO\Core\GDT;
use GDO\Core\Module_Core;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\User\GDO_User;
use GDO\User\GDT_ACLRelation;
use GDO\User\Method\Profile;
use GDO\User\Module_User;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

final class UserTest extends TestCase
{

//	public function testSystemUser()
//	{
////		$u1 = Module_Core::instance()->cfgSystemUser();
////		$u2 = GDO_User::system();
////		assertTrue($u1 === $u2, 'Test single identity cache with system user.');
////
////		$id = Module_Core::instance()->cfgSystemUserID();
////		assertEquals($id, $u1->getID(), 'Test single identity cache with config system user.');
//	}

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
		Module_User::instance()->saveUserSettingACLRelation($user, 'gender', GDT_ACLRelation::ALL);
		$me = GDT_MethodTest::make()->method(Profile::make())->inputs(['for' => 'gizmore']);
		$result = $me->execute();
		$html = $result->renderMode(GDT::RENDER_WEBSITE);
		assertGreaterThanOrEqual(1, substr_count($html, 'male'), 'Test if gizmore can change to male');
	}

}
