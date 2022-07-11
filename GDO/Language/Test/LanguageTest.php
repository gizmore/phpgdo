<?php
namespace GDO\Language\Test;

use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\Crypto\BCrypt;
use GDO\Language\Module_Language;
use GDO\Language\Trans;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertFalse;

/**
 * Configure the Language module for 3 test languages.
 * Final Step for the test module chain.
 * All modules with a priority larger than Module_Language do not care.
 * 
 * Rudimentary I18n test.
 * Permission and $TEST_USERS generation.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.3.4
 * @see Module_Language
 * @see GDT_MethodTest
 */
final class LanguageTest extends TestCase
{
    public function testLanguage()
    {
        $module = Module_Language::instance();
        $module->saveConfigVar('languages', '["de","en","it"]');
        $languages = $module->cfgSupported();
        assertCount(3, $languages, 'Check if 3 languages can be supported via Language config.');
        
        $de1 = tiso('de', 'lang_de');
        $en1 = tiso('en', 'lang_de');
        assertNotEquals($de1, $en1, 'english should differ from german');
        
        Trans::setISO('de');
        $de2 = t('lang_de');
        Trans::setISO('en');
        $en2 = t('lang_de');
        assertNotEquals($de1, $en1, 'german should differ from english');
        assertEquals($de1, $de2, 'german should be identical');
        assertEquals($en1, $en2, 'english should be identical');
    }
    
    public function testHTTPLangDetection()
    {
        $iso = Module_Language::instance()->detectAcceptLanguage();
        assertEquals('de', $iso, 'Test if german language is detected.');
    }
    
    /**
     * In this test we can finally generate all our test users.
     * If this fails, Houston(TX) has a problem.
     */
    public function testUserGeneration()
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
    	GDT_MethodTest::$TEST_USERS[] = $user;
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
    	GDT_MethodTest::$TEST_USERS[] = $user;
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
    	GDT_MethodTest::$TEST_USERS[] = $user;
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
    	GDT_MethodTest::$TEST_USERS[] = $user;
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
    	GDT_MethodTest::$TEST_USERS[] = $user;
    	$user->changedPermissions();
    	assertFalse($user->isGhost(), "Test if staff is not ghost.");
    	assertFalse($user->isGuest(), "Test if staff is not guest.");
    	assertFalse($user->isAdmin(), "Test if staff is not admin.");
    	assertTrue($user->isStaff(), "Test if staff has staff permissions assigned correctly.");
    	assertTrue($user->isMember(), "Test if staff is a member.");
    }
}
