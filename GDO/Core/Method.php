<?php
namespace GDO\Core;

use GDO\DB\Database;
use GDO\UI\GDT_Error;
use GDO\UI\GDT_Success;
use GDO\User\GDO_User;
use GDO\Util\Strings;
use GDO\UI\WithTitle;
use GDO\UI\WithDescription;
use GDO\CLI\CLI;
use GDO\UI\GDT_Page;
use GDO\Language\Trans;
use GDO\UI\GDT_Redirect;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Form;
use GDO\Date\Time;
use GDO\Session\GDO_Session;

/**
 * Abstract baseclass for all methods.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.1
 * @see WithParameters
 */
abstract class Method #extends GDT
{
	use WithTitle;
	use WithInput;
	use WithModule;
	use WithParameters;
	use WithDescription;
	
	public function getName() : ?string
	{
		return $this->gdoClassName();
	}
	
	################
	### Override ###
	################
	# execution
	public function isEnabled() : bool { return $this->getModule()->isEnabled(); }
	public function getMethodName() : string { return $this->gdoShortName(); }
	public function getPermission() : ?string { return null; }
	public function hasPermission(GDO_User $user) : bool { return true; }
	
	public abstract function execute();
	
	protected function executeB()
	{
		return $this->execute();
	}
	
	public function getCLITrigger()
	{
		$trigger = "{$this->getModuleName()}.{$this->getMethodName()}";
		return strtolower($trigger);
	}

	# toggles
	public function isCLI() : bool { return false; }
	public function isAjax() : bool { return false; }
	/**
	 * Toggle if method can be trivially fuzz-tested. defaults to yes.
	 */
	public function isTrivial() : bool { return true; }
	public function getUserType() : ?string { return null; }
	public function isUserRequired() : bool { return false; }
	public function isGuestAllowed() : bool { return Module_Core::instance()->cfgAllowGuests(); }
	public function isTransactional() : bool { return Application::$INSTANCE->verb === GDT_Form::POST; }
	public function isAlwaysTransactional() : bool { return false; }
	public function isSavingLastUrl() : bool { return true; }
	public function isShownInSitemap() : bool { return true; }
	
	# events
	public function onInit() {}
	public function beforeExecute() : void {}
	public function afterExecute() : void {}
	
	###################
	### Alias Cache ###
	###################
	/**
	 * @var Method[string]
	 */
	public static array $CLI_ALIASES = [];
	
	public static function addCLIAlias(string $alias, string $className) : void
	{
		self::$CLI_ALIASES[$alias] = $className;
	}
	
	public function getAutoButton($keys) : ?string
	{
		$first = null;
		foreach ($this->gdoParameterCache() as $key => $gdt)
		{
			if ($gdt instanceof GDT_Submit)
			{
				if (in_array($key, $keys, true))
				{
					return $key;
				}
				if (!$first)
				{
					$first = $gdt->getName();
				}
			}
		}
		return $first;
	}
	
	
	############
	### HREF ###
	############
	public function href(string $append='')
	{
		return $this->getModule()->href($this->getMethodName(), $append);
	}
	
	/**
	 * Get a method by cli convention. Aliases first, then module DOT method.
	 * 
	 * @param string $alias
	 * @return Method
	 */
	public static function getMethod(string $alias, bool $throw=true) : ?self
	{
		$alias = strtolower($alias);
		if (isset(self::$CLI_ALIASES[$alias]))
		{
			$klass = self::$CLI_ALIASES[$alias];
			return $klass::make();
		}
		else
		{
			$moduleName = Strings::substrTo($alias, '.', $alias);
			
			if (!($module = ModuleLoader::instance()->getModule($moduleName)))
			{
				if ($throw)
				{
					throw new GDO_Error('err_unknown_module', [html($moduleName)]);
				}
				return null;
			}
			
			$methodName = Strings::substrFrom($alias, '.', t('none'));
			if ($method = $module->getMethod($methodName))
			{
				return $method;
			}
			if ($throw)
			{
				throw new GDO_Error('err_unknown_method', [$module->getName(), html($methodName)]);
			}
		}
		return null;
	}

	###################
	### Instanciate ###
	###################
	public static function make() : self
	{
		return new static();
	}
	
	############
	### Exec ###
	############
	
	public function checkPermission(GDO_User $user)
	{
		if (!($this->isEnabled()))
		{
			return $this->error('err_method_disabled', [$this->getModuleName(), $this->getMethodName()]);
		}
		
		if ( (!$this->isGuestAllowed()) && (!$user->isMember()) )
		{
			return $this->error('err_members_only');
		}
		
		if ( ($this->isUserRequired()) && (!$user->isUser()) )
		{
			if (GDO_Module::config_var('Register', 'guest_signup', '0'))
			{
				$hrefGuest = href('Register', 'Guest', "&_backto=".urlencode($_SERVER['REQUEST_URI']));
				return $this->error('err_user_required', [$hrefGuest]);
			}
			else
			{
				return $this->error('err_members_only');
			}
		}
		
		if ($mt = $this->getUserType())
		{
			$mt = explode(',', $mt);
			$ut = $user->getType();
			if (!in_array($ut, $mt, true))
			{
				return $this->error('err_user_type', [$this->getUserType()]);
			}
		}
		
		if ( ($permission = $this->getPermission()) && (!$user->hasPermission($permission)) )
		{
			return $this->error('err_permission_required', [t('perm_'.$permission)]);
		}
		
		if (!$this->hasPermission($user))
		{
			return $this->error('err_no_permission');
		}
		
		return true;
	}
	
	/**
	 * Test permissions and execute method.
	 */
	public function exec()
	{
		$user = GDO_User::current();
		
		if (true !== ($error = $this->checkPermission($user)))
		{
			return $error;
		}
		
		return $this->execWrap();
	}
	
	public function execMethod()
	{
		return $this->execWrap();
	}
	
	public function transactional()
	{
		return
			($this->isAlwaysTransactional()) ||
			($this->isTransactional() &&
				(Application::$INSTANCE->verb === GDT_Form::POST) );
	}
	
	/**
	 * Wrap execution in transaction if desired from method.
	 * @throws \Exception
	 * @return GDT_Response
	 */
	public function execWrap()
	{
		# Exec
		if ($response = $this->executeWithInit())
		{
// 			if ($response->hasError())
// 			{
// 				$this->setupSEOError();
// 			}
// 			else
// 			{
				$this->setupSEO();
// 			}
		}
		return $response;
	}
	
	public function executeWithInputs(array $inputs=null)
	{
		$this->inputs = $inputs;
		return $this->executeWithInit();
	}
	
	/**
	 * Execute this method with all hooks.
	 */
	public function executeWithInit()
	{
		$db = Database::instance();
		$response = GDT_Response::make();
		try
		{
			$transactional = false;
			
			# Init method
			if ($result = $this->onInit())
			{
				$response->addField($result);
			}
			
			# Wrap transaction start
			$transactional = $this->transactional();
			if ($transactional)
			{
				$db->transactionBegin();
			}
			
			$this->applyInput();

			if (Application::isError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			$this->beforeExecute();
			
			$result = GDT_Hook::callHook('BeforeExecute', $this, $response);
			
			$response->addField($result);
			
			if ($response->hasError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			if ($result = $this->executeB())
			{
				$response->addField($result);
			}
			
			if (Application::isSuccess())
			{
				$this->afterExecute();
				GDT_Hook::callHook('AfterExecute', $this, $response);
				if (Application::isSuccess())
				{
					if ($transactional)
					{
						$db->transactionEnd();
						$transactional = false;
					}
				}
			}
			
			# Wrap transaction end
			if (Application::isError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
			}
			
			return $response;
		}
		catch (GDO_PermissionException $e)
		{
			if ($transactional)
			{
				$db->transactionRollback();
			}
			return $this->errorRaw($e->getMessage());
		}
		catch (\Throwable $e)
		{
			if ($transactional)
			{
				$db->transactionRollback();
			}
			throw $e;
		}
	}
	
	###########
	### SEO ###
	###########
	public function setupSEO()
	{
		# SEO
		Website::setTitle($this->getMethodTitle());
		Website::addMeta(['keywords', $this->getMethodKeywords(), 'name']);
		Website::addMeta(['description', $this->getMethodDescription(), 'name']);
		
		# Store last URL in session
		if ($this->isSavingLastUrl())
		{
			$this->storeLastURL();
			$this->storeLastActivity();
		}
	}
	
	public function getMethodTitle() : string
	{
		$key = sprintf('mt_%s_%s', $this->getModuleName(), $this->getMethodName());
		$key = strtolower($key);
		return Trans::hasKey($key) ? t($key) : '';
	}
	
	public function getMethodDescription() : string
	{
		$key = sprintf('md_%s_%s', $this->getModuleName(), $this->getMethodName());
		$key = strtolower($key);
		return Trans::hasKey($key) ? t($key) : $this->getMethodTitle();
	}
	
	public function getMethodKeywords() : string
	{
		$keywords = [];
		if (Trans::hasKey('site_keywords'))
		{
			$keywords[] = t('site_keywords');
		}
		$key = sprintf('mk_%s_%s', $this->getModuleName(), $this->getMethodName());
		if (Trans::hasKey($key))
		{
			$keywords[] = t($key);
		}
		return implode(', ', $keywords);
	}
	
	##################
	### Statistics ###
	##################
	public function storeLastURL() : void
	{
// 		$user = GDO_User::current();
		if (class_exists('GDO\\Session\\GDO_Session', false))
		{
			GDO_Session::set('sess_last_url', $_SERVER['REQUEST_URI']);
		}
// 		->saveSettingVar('User', 'last_url', $var)
	}

	/**
	 * Update user last activity timestamp, for persisted users/guests.
	 */
	public function storeLastActivity() : void
	{
		$user = GDO_User::current();
		if ($user->isPersisted())
		{
			$time = Application::$TIME;
			$time -= $time % 60;
			$date = Time::getDate($time);
			$user->saveVar('user_last_activity', $date);
			$user->saveSettingVar('User', 'last_activity', $date);
		}
	}
	
	
	###################
	### Apply Input ###
	###################
	/**
	 * Get plug variables.
	 * @return string[string]
	 */
	public function plugVars() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
	public function withAppliedInputs(array $inputs) : self
	{
		$this->inputs($inputs);
		$this->applyInput();
		return $this;
	}
	
	protected function applyInput()
	{
		$inputs = $this->getInputs();
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$gdt->inputs($inputs);
		}
	}
	
	public function hasFields() : bool
	{
		return false;
	}
	
	#############
	### Error ###
	#############
	public function response(string $response) : GDT_String
	{
		return GDT_String::make()->var($response);
	}
	
	public function message($key, array $args = null, int $code = 200, bool $log = true) : GDT
	{
		return $this->success($key, $args, $code, $log);
	}
	
	public function success($key, array $args = null, int $code = 200, bool $log = true) : GDT
	{
		Application::setResponseCode($code);
		if (Application::$INSTANCE->isCLI())
		{
			echo t($key, $args) . "\n";
		}
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		$success = GDT_Success::make()->titleRaw($this->getModuleName())->text($key, $args);
		$top = GDT_Page::instance()->topResponse();
		$top->addField($success);
		$result = GDT_Tuple::make();
		return $result;
	}
	
	public function error(string $key, array $args = null, int $code = GDO_Exception::DEFAULT_ERROR_CODE, bool $log = true) : GDT
	{
		Application::setResponseCode($code);
		if (Application::$INSTANCE->isCLI())
		{
			echo CLI::red(t($key, $args)) . "\n";
		}
		if ($log)
		{
			Logger::logError(ten($key, $args));
		}
		$error = GDT_Error::make()->titleRaw($this->getModule()->gdoHumanName())->text($key, $args);
		$top = GDT_Page::instance()->topResponse();
		$top->addField($error);
		$response = GDT_Response::make();
		return $response;
	}
	
	public function errorRaw(string $message, int $code = GDO_Exception::DEFAULT_ERROR_CODE, bool $log = true) : GDT
	{
		Application::setResponseCode($code);
		if ($log)
		{
			Logger::logError($message);
		}
		return GDT_Error::make()->titleRaw($this->getModuleName())->textRaw($message);
	}
	
	################
	### Redirect ###
	################
	public function redirect(string $href) : GDT_Redirect
	{
		return GDT_Redirect::make()->href($href);
	}
	
	public function redirectBack(string $default = null) : GDT_Redirect
	{
		$href = GDT_Redirect::make()->hrefBack($default);
		return $this->redirect($href);
	}
	
	public function redirectMessage(string $key, array $args = null, string $href=null) : GDT_Redirect
	{
		return GDT_Redirect::make()->href($href)->redirectMessage($key, $args);
	}
	
	public function redirectError(string $key, array $args = null, string $href) : GDT_Redirect
	{
		return GDT_Redirect::make()->href($href)->redirectError($key, $args);
	}
	
	################
	### Template ###
	################
	public function templatePHP(string $path, array $tVars=null) : GDT_Template
	{
		return GDT_Template::make()->template($this->getModuleName(), $path, $tVars);
	}

}
