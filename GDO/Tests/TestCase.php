<?php
namespace GDO\Tests;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use GDO\CLI\CLI;
use GDO\Core\GDT_Expression;
use GDO\User\GDO_User;
use GDO\Session\GDO_Session;
use GDO\Core\Method;
use GDO\Net\GDT_IP;
use GDO\User\Module_User;
use GDO\User\GDO_UserPermission;
use GDO\Core\Application;
use GDO\Util\FileUtil;
use GDO\Language\Trans;
use GDO\Date\Time;
use GDO\Date\GDO_Timezone;
use PHPUnit\Framework\Assert;
use GDO\Core\WithModule;

/**
 * A GDO test case knows a few helper functions.
 * Sets up a clean response environment.
 * Allows user and language switching.
 * 
 * Cycles IPs
 * 
 * Provides GDT_MethodTest for convinient testing.
 * Adds cli() test function for convinient testing.
 * Adds proc() test function for convinient testing.
 * 
 * Provides MethodTest->execute() helper for convinient testing.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.1
 * @see MethodTest
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
	use WithModule;
	
	public static int $LAST_COUNT = 0;
	public static int $ASSERT_COUNT = 0;
// 	public static int $ASSERT_FAILS = 0; # @TODO: calculate assert fails.
	
    #################
    ### Init test ###
    #################
    private int $ipc = 0;
    private int $ipd = 0;
    private function nextIP()
    {
        $this->ipd++;
        if ($this->ipd>254)
        {
            $this->ipd = 1;
            $this->ipc++;
        }
        $ip = sprintf('127.0.%d.%d', $this->ipc, $this->ipd);
        return $ip;
    }
    
    protected function setUp(): void
    {
    	$this->message("Running %s", CLI::bold($this->gdoClassName()));
    	
        Application::$INSTANCE->reset();
        
        # Increase IP
        GDT_IP::$CURRENT = $this->nextIP();
        
        # Set gizmore user
        if (Module_User::instance()->isPersisted())
        {
            $user = count(GDT_MethodTest::$TEST_USERS) ? GDT_MethodTest::$TEST_USERS[0] : GDO_User::system();
            $this->user($user);
            if (!$user->isSystem())
            {
                $this->restoreUserPermissions($user);
            }
        }
    }
    
    protected function tearDown() : void
    {
    	$new = Assert::getCount();
    	$add = $new - self::$LAST_COUNT;
    	self::$ASSERT_COUNT += $add;
//     	self::$LAST_COUNT = self::$ASSERT_COUNT;
    }
    
    /**
     * Restore gizmore because auto coverage messes with him a lot.
     * @param GDO_User $user
     */
    protected function restoreUserPermissions(GDO_User $user) : void
    {
        if (count(GDT_MethodTest::$TEST_USERS))
        {
        	if ($user->getID() === GDT_MethodTest::$TEST_USERS[0]->getID())
            {
                $table = GDO_UserPermission::table();
                $table->grant($user, 'admin');
                $table->grant($user, 'staff');
                $table->grant($user, 'cronjob');
                $user->changedPermissions();
                $user->saveVar('user_deleted', null);
                $user->saveVar('user_deletor', null);
            }
        }
    }
    
    /**
     * @var GDO_Session[]
     */
    protected array $sessions = [];
    
    protected function session(GDO_User $user) : GDO_Session
    {
        $uid = $user->getID();
        if (!isset($this->sessions[$uid]))
        {
            $this->sessions[$uid] = GDO_Session::blank();
            $this->sessions[$uid]->setVar('sess_user', $user->getID());
        }
        GDO_Session::$INSTANCE = $this->sessions[$uid];
        return $this->sessions[$uid];
    }

    ###################
    ### User switch ###
    ###################
    protected function ghost() : GDO_User { return GDO_User::ghost(); }
    protected function system() : GDO_User { return GDO_User::system(); } # 1
    protected function gizmore() : GDO_User { return GDT_MethodTest::$TEST_USERS[0]; } # user_id: 2
    protected function peter() : GDO_User { return GDT_MethodTest::$TEST_USERS[1]; } # 3
    protected function monica() : GDO_User { return GDT_MethodTest::$TEST_USERS[2]; } # 4
    protected function gaston() : GDO_User { return GDT_MethodTest::$TEST_USERS[3]; } # 5
    protected function sven() : GDO_User { return GDT_MethodTest::$TEST_USERS[4]; } # 6
    
    protected function userGhost() : GDO_User { return $this->user(GDO_User::ghost()); } # ID 0
    protected function userSystem() : GDO_User { return $this->user(GDO_User::system()); } # ID 1 
    protected function userGizmore() : GDO_User { return $this->user($this->gizmore()); } # Admin 
    protected function userPeter() : GDO_User { return $this->user($this->peter()); } # Staff
    protected function userMonica() : GDO_User { return $this->user($this->monica()); } # Member
    protected function userGaston() : GDO_User { return $this->user($this->gaston()); } # Guest
    protected function userSven() : GDO_User { return $this->user($this->sven()); }
    
    protected function user(GDO_User $user) : GDO_User
    {
        $this->session($user);
        Trans::setISO($user->getLangISO());
        Time::setTimezone($user->getTimezone());
        return GDO_User::setCurrent($user);
    }
    
    ###############
    ### Asserts ###
    ###############
    protected function assert200(string $message) { $this->assertCode(200, $message); }
    protected function assert409(string $message) { $this->assertCode(409, $message); }
    protected function assertCode(int $code, string $message)
    {
    	if (Application::isError())
    	{
    		CLI::flushTopResponse();
    	}
        assertEquals($code, Application::$RESPONSE_CODE, $message);
    }
    
    protected function assertStringContainsStrings(array $needles, string $haystack, string $message='')
    {
        foreach ($needles as $needle)
        {
            assertStringContainsString($needle, $haystack, $message . "; $needle not found!");
        }
    }
    
    protected function assertStringContainsStringsIgnoringCase(array $needles, string $haystack, string $message='')
    {
        foreach ($needles as $needle)
        {
            assertStringContainsStringIgnoringCase($needle, $haystack, $message . "; $needle not found!");
        }
    }
    
    ###################
    ### Call method ###
    ###################
    protected function callMethod(Method $method, array $parameters=null, array $getParameters=null)
    {
        $gdt_method = GDT_MethodTest::make()->method($method)->runAs()->addFields(...$getParameters)->addFields(...$parameters);
        $result = $gdt_method->execute();
        $gdt_method->result($result);
        $this->assert200(sprintf('Test if %s response code is 200.', $method->gdoClassName()));
        return $result;
    }
    
    protected function fakeFileUpload($fieldName, $fileName, $path)
    {
        $dest = Module_Tests::instance()->tempPath($fileName);
        copy($path, $dest);
        $_FILES[$fieldName] = [
            'name' => $fileName,
            'type' => FileUtil::mimetype($dest),
            'tmp_name' => $dest,
            'error' => 0,
            'size' => filesize($dest),
        ];
    }
    
    #################
    ### CLI Tests ###
    #################
    public function proc(string $command) : string
    {
    	$output = [];
    	$retval = -2;
    	exec($command, $output, $retval);
    	assertEquals(0, $retval, 'Assert that this process works: ' . $command);
    	$output = implode("\n", $output);
    	$output .= $output ? "\n" : '';
    	return $output;
    }
    
    public function cli(string $command, bool $permissions=true) : string
    {
    	$app = Application::$INSTANCE;
    	$app->reset();
    	$app->cli(true);
//     	$_POST = ['a' => '1'];
       	$expression = GDT_Expression::fromLine($command);
    	$response = $expression->execute();
    	$result = $response->renderCLI();
    	return $result;
    }
    
    ############
    ### Lang ###
    ############
    public function lang($iso)
    {
        Trans::setISO($iso);
    }
    
    public function timezone($tz)
    {
    	$tz = GDO_Timezone::getBy('tz_name', $tz);
        Time::setTimezone($tz->getID());
    }
    
    ##############
    ### Output ###
    ##############
    protected function error($message, ...$args)
    {
    	fwrite(STDERR, vsprintf($message, $args));
    	fwrite(STDERR, "\n");
    	ob_flush();
    }
    
    protected function messageBold($message, ...$args)
    {
    	return $this->message(CLI::bold($message), ...$args);
    }
    
    protected function message($message, ...$args)
    {
    	echo vsprintf($message, $args);
    	echo "\n";
    	ob_flush();
    }
    
    protected function boldmome(Method $method)
    {
    	return CLI::bold(self::mome($method));
    }
    
    protected function mome(Method $method)
    {
    	return sprintf('%s/%s', $method->getModuleName(), $method->getMethodName());
    }
    
}
