<?php
namespace GDO\Tests;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use GDO\Core\GDT_Response;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;
use GDO\Session\GDO_Session;
use GDO\Core\Method;
use GDO\Net\GDT_IP;
use GDO\User\Module_User;
use GDO\User\GDO_UserPermission;
use GDO\Core\Application;
use GDO\File\FileUtil;
use GDO\Language\Trans;
use GDO\CLI\CLI;
use GDO\Core\Website;
use GDO\Date\Time;
use GDO\Date\GDO_Timezone;

/**
 * A GDO test case knows a few helper functions.
 * Sets up a clean response environment.
 * Allows user switching.
 * Adds cli test function for convinient testing.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.1
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    #################
    ### Init test ###
    #################
    private $ipc = 0;
    private $ipd = 0;
    private function nextIP()
    {
        $this->ipd++;
        if ($this->ipd>255)
        {
            $this->ipd = 1;
            $this->ipc++;
        }
        $ip = sprintf('127.0.%d.%d', $this->ipc, $this->ipd);
        return $ip;
    }
    
    protected function setUp(): void
    {
        # Increase Time
        Application::updateTime();
        
        # Increase IP
        GDT_IP::$CURRENT = $this->nextIP();
        
        # Clear input
        $_REQUEST = $_POST = $_GET = $_FILES = [];
        
        # Clear code
        GDT_Response::$CODE = 200;
        
        # Clear navs
        $p = GDT_Page::$INSTANCE;
        $p->reset();
        GDT_Response::newWith();
        
        # Set gizmore user
        if (Module_User::instance()->isPersisted())
        {
            $user = count(MethodTest::$USERS) ? MethodTest::$USERS[0] : GDO_User::system();
            if ($user)
            {
                $this->user($user);
                if (!$user->isSystem())
                {
                    $this->restoreUserPermissions($user);
                }
            }
        }
    }
    
    /**
     * Restore gizmore because auto coverage messes with him a lot.
     * @param GDO_User $user
     */
    protected function restoreUserPermissions(GDO_User $user)
    {
        if (count(MethodTest::$USERS))
        {
            if ($user->getID() === MethodTest::$USERS[0]->getID())
            {
                $table = GDO_UserPermission::table();
                $table->grant($user, 'admin');
                $table->grant($user, 'staff');
                $table->grant($user, 'cronjob');
                $user->changedPermissions();
            }
        }
    }
    
    protected $sessions = [];
    
    protected function session(GDO_User $user)
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
    protected function ghost() { return GDO_User::ghost(); }
    protected function system() { return GDO_User::system(); }
    protected function gizmore() { return MethodTest::$USERS[0]; } # 2
    protected function peter() { return MethodTest::$USERS[1]; } # 3
    protected function monica() { return MethodTest::$USERS[2]; } # 4
    protected function gaston() { return MethodTest::$USERS[3]; } # 5
    protected function sven() { return MethodTest::$USERS[4]; } # 6
    
    protected function userGhost() { return $this->user(GDO_User::ghost()); } # ID 0
    protected function userSystem() { return $this->user(GDO_User::system()); } # ID 1 
    protected function userGizmore() { return $this->user($this->gizmore()); } # Admin 
    protected function userPeter() { return $this->user($this->peter()); } # Staff
    protected function userSven()
    {
        $user = $this->user($this->sven());
//         GDO_UserPermission::grant($user, 'staff');
//         $user->changedPermissions();
        return $user;
    }
    protected function userMonica() { return $this->user($this->monica()); } # Member
    protected function userGaston() { return $this->user($this->gaston()); } # Guest
    
    protected function user(GDO_User $user)
    {
        $this->session($user);
        Trans::setISO($user->getLangISO());
        Time::setTimezone($user->getTimezone());
        return GDO_User::setCurrent($user);
    }
    
    ###################
    ### Assert code ###
    ###################
    protected function assert200($message) { $this->assertCode(200, $message); }
    protected function assert409($message) { $this->assertCode(409, $message); }
    protected function assertCode($code, $message)
    {
    	$message .= 'OUT: ' . Website::renderTopResponse();
        assertEquals($code, GDT_Response::$CODE, $message);
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
        $r = MethodTest::make()->method($method)->user(GDO_User::current())->parameters($parameters)->getParameters($getParameters)->execute();
        $this->assert200(sprintf('Test if %s::%s response code is 200.', 
            $method->getModuleName(), $method->getMethodName()));
        return $r;
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
    public function cli($command)
    {
        try
        {
            # Clean
            $_REQUEST = $_GET = $_POST = [];
            GDT_Page::$INSTANCE->reset();
            GDT_Response::newWith();
            Application::instance()->cli(true);
            
            # Exec
            ob_start();
            echo CLI::execute($command)->renderCLI();
            $top = '';
            $response = ob_get_contents();
            if (Website::$TOP_RESPONSE)
            {
                $top = Website::$TOP_RESPONSE->renderCLI() . "\n";
            }
            return $top . $response;
        }
        catch (\Throwable $ex)
        {
            throw $ex;
        }
        finally
        {
            Application::instance()->cli(false);
            ob_end_clean();
        }
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
    
}
