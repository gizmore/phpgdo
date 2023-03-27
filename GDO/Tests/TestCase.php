<?php
declare(strict_types=1);
namespace GDO\Tests;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDT;
use GDO\Core\GDT_Expression;
use GDO\Core\Method;
use GDO\Core\WithModule;
use GDO\Crypto\BCrypt;
use GDO\Date\GDO_Timezone;
use GDO\Date\Time;
use GDO\Form\GDT_Form;
use GDO\Language\Trans;
use GDO\Net\GDT_IP;
use GDO\Session\GDO_Session;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\User\Module_User;
use GDO\Util\FileUtil;
use PHPUnit\Framework\Assert;
use Throwable;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertLessThan;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;

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
 * @version 7.0.3
 * @since 6.10.1
 * @author gizmore
 * @see MethodTest
 */
class TestCase extends \PHPUnit\Framework\TestCase
{

	use WithModule;

	public static int $LAST_COUNT = 0;

	public static int $ASSERT_COUNT = 0;

	// public static int $ASSERT_FAILS = 0; # @TODO: calculate assert fails.

	# ################
	# ## Init test ###
	# ################
	/**
	 *
	 * @var GDO_Session[]
	 */
	protected array $sessions = [];
	protected array $plugVariants;
	private int $ipc = 0;
	private int $ipd = 0;

	public function proc(string $command): string
	{
		$output = [];
		$retval = -2;
		exec($command, $output, $retval);
		assertEquals(0, $retval, 'Assert that this process works: ' . $command);
		$output = implode("\n", $output);
		$output .= $output ? "\n" : '';
		return $output;
	}

	public function cli(string $command, bool $permissions = true): string
	{
		$app = Application::$INSTANCE;
		$app->reset();
		$app->cli();
		$expression = GDT_Expression::fromLine($command);
		$response = $expression->execute();
		$app->cli(false);
		$res = CLI::getTopResponse();
		$res .= $response->renderCLI();
		if (Application::isError())
		{
			$res .= CLI::renderCLIHelp($expression->method->method);
		}
		return trim($res, "\r\n");
	}

	public function lang($iso): void
	{
		Trans::setISO($iso);
	}

	public function timezone($tz): void
	{
		$tz = GDO_Timezone::getBy('tz_name', $tz);
		Time::setTimezone($tz->getID());
	}

	protected function setUp(): void
	{
		$this->message("\nRunning %s", CLI::bold($this->gdoClassName()));

		$app = Application::$INSTANCE;
		$app->reset();
		$app->verb(GDT_Form::GET);

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

	# ##################
	# ## User switch ###
	# ##################

	protected function message($message, ...$args): void
	{
		$this->out(STDOUT, $message, $args);
	}

	/**
	 * @param resource $fh
	 */
	private function out($fh, string $message, array $args): void
	{
		fwrite($fh, vsprintf($message, $args));
		fwrite($fh, "\n");
		flush();
		if (ob_get_level())
		{
			ob_flush();
		}
	}

	# 1

	private function nextIP(): string
	{
		$this->ipd++;
		if ($this->ipd > 254)
		{
			$this->ipd = 1;
			$this->ipc++;
		}
		return sprintf('127.0.%d.%d', $this->ipc, $this->ipd);
	}

	# user_id: 2

	protected function system(): GDO_User
	{
		return GDO_User::system();
	}

	# 3

	protected function user(GDO_User $user): GDO_User
	{
		$this->session($user);
		Trans::setISO($user->getLangISO());
		Time::setTimezone($user->getTimezone());
		return GDO_User::setCurrent($user);
	}

	# 4

	protected function session(GDO_User $user): ?GDO_Session
	{
		if (module_enabled('Session'))
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
		return null;
	}

	# 5

	/**
	 * Restore gizmore because auto coverage messes with him a lot.
	 *
	 * @param GDO_User $user
	 */
	protected function restoreUserPermissions(GDO_User $user): void
	{
		if (count(GDT_MethodTest::$TEST_USERS))
		{
			# IF GIZMORE
			if ($user->getID() === GDT_MethodTest::$TEST_USERS[0]->getID())
			{
				$table = GDO_UserPermission::table();
				$table->grant($user, 'admin');
				$table->grant($user, 'staff');
				$table->grant($user, 'cronjob');
				$user->changedPermissions();
				$user->saveVar('user_deleted', null);
				$user->saveVar('user_deletor', null);

				$this->restoreUserSettings($user);
			}
		}
	}

	# 6

	protected function restoreUserSettings(GDO_User $user): void
	{
		# english and male
		$user->saveSettingVar('User', 'gender', 'male');
// 		$user->saveSettingVar('Country', 'country', 'DE');
		$user->saveSettingVar('Language', 'language', GDO_LANGUAGE);
		$hash = BCrypt::create('11111111')->__toString();
		$user->saveSettingVar('Login', 'password', $hash);
	}

	# ID 0

	protected function tearDown(): void
	{
		$new = Assert::getCount();
		$add = $new - self::$LAST_COUNT;
		self::$ASSERT_COUNT += $add;
		// self::$LAST_COUNT = self::$ASSERT_COUNT;
		CLI::flushTopResponse();
	}

	# ID 1

	protected function userGhost(): GDO_User
	{
		return $this->user(GDO_User::ghost());
	}

	# Admin

	protected function ghost(): GDO_User
	{
		return GDO_User::ghost();
	}

	# Staff

	protected function userSystem(): GDO_User
	{
		return $this->user(GDO_User::system());
	}

	# Member

	protected function userGizmore(): GDO_User
	{
		return $this->user($this->gizmore());
	}

	# Guest

	protected function gizmore(): GDO_User
	{
		return GDT_MethodTest::$TEST_USERS[0];
	}

	protected function userPeter(): GDO_User
	{
		return $this->user($this->peter());
	}

	# ##############
	# ## Asserts ###
	# ##############

	protected function peter(): GDO_User
	{
		return GDT_MethodTest::$TEST_USERS[1];
	}

	protected function userMonica(): GDO_User
	{
		return $this->user($this->monica());
	}

	protected function monica(): GDO_User
	{
		return GDT_MethodTest::$TEST_USERS[2];
	}

	protected function userGaston(): GDO_User
	{
		return $this->user($this->gaston());
	}

	protected function gaston(): GDO_User
	{
		return GDT_MethodTest::$TEST_USERS[3];
	}

	protected function userSven(): GDO_User
	{
		return $this->user($this->sven());
	}

	protected function sven(): GDO_User
	{
		return GDT_MethodTest::$TEST_USERS[4];
	}

	protected function assertNoCrash(string $message): void
	{
		assertLessThan(500, Application::$RESPONSE_CODE, $message);
	}

	# ##################
	# ## Call method ###
	# ##################
// 	protected function callMethod(Method $method, array $parameters = null, array $getParameters = null)
// 	{
// 		$gdt_method = GDT_MethodTest::make()->method($method)
// 			->runAs($method->plugUser())
// 			->addFields(...$getParameters)
// 			->addFields(...$parameters);
// 		$result = $gdt_method->execute();
// 		$gdt_method->result($result);
// 		$this->assert200(sprintf('Test if %s response code is 200.', $method->gdoClassName()));
// 		return $result;
// 	}

	protected function assert200(string $message): void
	{
		$this->assertCode(200, $message);
	}

	protected function assertCode(int $code, string $message): void
	{
		try
		{
			assertEquals($code, Application::$RESPONSE_CODE, $message);
		}
		catch (Throwable $ex)
		{
			echo Debug::debugException($ex);
			throw $ex;
		}
	}

	# ################
	# ## CLI Tests ###
	# ################

	protected function assert403(string $message): void
	{
		$this->assertCode(403, $message);
	}

	protected function assertStringContainsStrings(array $needles, string $haystack, string $message = ''): void
	{
		foreach ($needles as $needle)
		{
			assertStringContainsString($needle, $haystack, $message . "; $needle not found!");
		}
	}

	# ###########
	# ## Lang ###
	# ###########

	protected function assertStringContainsStringsCI(array $needles, string $haystack, string $message = ''): void
	{
		foreach ($needles as $needle)
		{
			assertStringContainsStringIgnoringCase($needle, $haystack, $message . "; $needle not found!");
		}
	}

	protected function callMethod(Method $method, array $inputs, bool $assertOk = true): GDT
	{
		$m = GDT_MethodTest::make()->method($method);
		$m->inputs($inputs);
		$r = $m->execute();
// 		$r->render(); # This will trigger 409 to be set -.-
		if ($assertOk)
		{
			$this->assertOK("Test if callMethod {$method->gdoClassName()} does not fail");
		}
		else
		{
			$this->assert409("Test if callMethod {$method->gdoClassName()} errors!");
		}
		return $r;
	}

	# #############
	# ## Output ###
	# #############

	protected function assertOK(string $message): void
	{
		assertLessThan(400, Application::$RESPONSE_CODE, $message);
	}

	protected function assert409(string $message): void
	{
		$this->assertCode(409, $message);
	}

	protected function fakeFileUpload($fieldName, $fileName, $path): void
	{
		$dest = Module_Tests::instance()->tempPath($fileName);
		$error = 5;
		if (FileUtil::isFile($path))
		{
			copy($path, $dest);
			$error = 0;
		}
		$_FILES[$fieldName] = [
			'name' => $fileName,
			'type' => FileUtil::mimetype($dest),
			'tmp_name' => $dest,
			'error' => $error,
			'size' => filesize($dest),
		];
	}

	protected function error($message, ...$args): void
	{
		$this->out(STDERR, $message, $args);
	}

	protected function boldmome(Method $method): string
	{
		return CLI::bold(self::mome($method));
	}

	# ###############
	# ## PlugVars ###
	# ###############

	protected function mome(Method $method): string
	{
		return sprintf('%s/%s', $method->getModuleName(), $method->getMethodName());
	}

	protected function addPlugVars(array $_plugs): void
	{
		foreach ($_plugs as $plugs)
		{
			if (!is_array($plugs))
			{
				xdebug_break();
			}
			$this->addPlugVarsB($plugs);
		}
	}

	private function addPlugVarsB(array $plugs): void
	{
		foreach ($plugs as $name => $plug)
		{
			if (!isset($this->plugVariants[$name]))
			{
				$this->plugVariants[$name] = [];
			}
			if (!in_array($plug, $this->plugVariants[$name], true))
			{
				$this->plugVariants[$name][] = $plug;
			}
		}
	}

}
