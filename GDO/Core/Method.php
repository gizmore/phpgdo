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

/**
 * Abstract baseclass for all methods.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.1
 * @see WithParameters
 */
abstract class Method #extends GDT
{
	use WithTitle;
	use WithInput;
	use WithModule;
	use WithInstance;
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
	
	# toggles
	public function isCLI() : bool { return false; }
	public function isAjax() { return false; }
	public function isTrivial() { return true; }
	public function saveLastUrl() { return true; }
	public function getUserType() : ?string { return null; }
	public function isUserRequired() : bool { return false; }
	public function isGuestAllowed() : bool { return Module_Core::instance()->cfgAllowGuests(); }
	public function isTransactional() : bool { return false; }
	public function isAlwaysTransactional() : bool { return false; }
	public function storeLastURL() : bool { return true; }
	public function storeLastActivity() : bool { return true; }
	
	# events
	public function onInit() : void {}
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
	public static function getMethod(string $alias, bool $throw=true) : self
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
			$methodName = Strings::substrFrom($alias, '.', $alias);
			$module = ModuleLoader::instance()->getModule($moduleName);
			if ($method = $module->getMethod($methodName))
			{
				return $method;
			}
			if ($throw)
			{
				throw new GDO_Error('err_unknown_method', [html($alias)]);
			}
		}
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
	/**
	 * Test permissions and execute method.
	 */
	public function exec()
	{
// 		if ($this->isAjax())
// 		{
// 			$_REQUEST['_ajax'] = '1';
// 		}
		
		$user = GDO_User::current();
		
		if (!($this->isEnabled()))
		{
			return GDT_Error::make()->text('err_method_disabled', [$this->getModuleName(), $this->getMethodName()]);
		}
		
		if ( (!$this->isGuestAllowed()) && (!$user->isMember()) )
		{
			return GDT_Error::make()->text('err_members_only');
		}
		
		if ( ($this->isUserRequired()) && (!$user->isAuthenticated()) )
		{
			if (GDO_Module::config_var('Register', 'guest_signup', '0'))
			{
				$hrefGuest = href('Register', 'Guest', "&_backto=".urlencode($_SERVER['REQUEST_URI']));
				return GDT_Error::make()->text('err_user_required', [$hrefGuest]);
			}
			else
			{
				return GDT_Error::make()->text('err_members_only');
			}
		}
		
		if ($mt = $this->getUserType())
		{
			$mt = explode(',', $mt);
			$ut = $user->getType();
			if (!in_array($ut, $mt, true))
			{
				return GDT_Error::make()->text('err_user_type', [$this->getUserType()]);
			}
		}
		
		if ( ($permission = $this->getPermission()) && (!$user->hasPermission($permission)) )
		{
			return GDT_Error::make()->text('err_permission_required', [t('perm_'.$permission)]);
		}
		
		if (!$this->hasPermission($user))
		{
			return GDT_Error::make('err_no_permission');
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
		($this->isTransactional() && (count($_POST)>0) );
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
			$this->setupSEO();
		}
		return $response;
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
		$this->storeLastURL();
		
		# Store last activity in user
		$this->storeLastActivity();
	}
	
	###
	public function getMethodTitle()
	{
		return 'TITLE';
	}
	
	public function getMethodKeywords()
	{
		return 'KEYWORDS';
	}
	
	public function getMethodDescription()
	{
		return 'DESCR';
	}
	
	###############
	### Execute ###
	###############
	/**
	 * Execute this method with all hooks.
	 */
	public function executeWithInit()
	{
		$db = Database::instance();
		$response = GDT_Response::make();
		$transactional = $this->transactional();
		try
		{
			# Wrap transaction start
			if ($transactional)
			{
				$db->transactionBegin();
			}
			
			# Apply input
			$this->applyInput();
			
			# Init method
			$this->inited = false;
			$this->onInit();
			
			if (Application::isError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			$this->beforeExecute();
			GDT_Hook::callHook('BeforeExecute', $this, $response);
			
			if ($response->hasError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			if ($result = $this->execute())
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
	
	###################
	### Apply Input ###
	###################
	/**
	 * Get plug variables.
	 * @return string[string]
	 */
	public function plugVars() : array
	{
		return GDT::EMPTY_GDT_ARRAY;
	}
	
	public function withAppliedInputs(array $inputs) : self
	{
		$this->addInputs($inputs);
		$this->applyInput();
		return $this;
	}
	
	private function applyInput()
	{
		foreach ($this->getInputs() as $key => $input)
		{
			if ($gdt = $this->gdoParameter($key, false, false))
			{
// 				if (is_array($input))
// 				{
// 					$input = json_encode($input);
// 				}
				$gdt->input($input);
			}
		}
	}
	
	#############
	### Error ###
	#############
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
		$response = GDT_Tuple::make();
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
	### Template ###
	################
	public function templatePHP(string $path, array $tVars=null) : GDT_Template
	{
		return GDT_Template::make()->template($this->getModuleName(), $path, $tVars);
	}
	

}
