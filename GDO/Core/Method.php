<?php
namespace GDO\Core;

use GDO\DB\Database;
use GDO\UI\GDT_Error;
use GDO\UI\GDT_Success;
use GDO\User\GDO_User;
use GDO\Util\Strings;
use GDO\UI\WithTitle;
use GDO\UI\WithDescription;

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
	use WithFields;
	use WithModule;
	use WithInstance;
	use WithParameters;
	use WithDescription;
	
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
	 * @var Method[]
	 */
	public static array $CLI_ALIASES = [];
	
	public static function addCLIAlias(string $alias, string $className) : void
	{
		self::$CLI_ALIASES[$alias] = $className;
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
	
	##################
	### Parameters ###
	##################
// 	/**
// 	 * @return GDT[]
// 	 */
// 	public function gdoParameters() : array
// 	{
// 		return GDT::EMPTY_ARRAY;
// 	}
	
// 	/**
// 	 * @return GDT[]
// 	 */
// 	protected function gdoParametersB() : array
// 	{
// 		return $this->gdoParameters();
// 	}
	
// 	/**
// 	 * Get a parameter GDT before the cache is generated.
// 	 * @return GDT|NULL
// 	 */
// 	protected function gdoParameterB(string $name) : ?GDT
// 	{
// 		foreach ($this->gdoParameters() as $gdt)
// 		{
// 			if ($gdt->getName() === $name)
// 			{
// 				return $gdt;
// 			}
// 		}
// 		return null;
// 	}
	
	
// 	/**
// 	 * @var GDT[]
// 	 */
// 	private array $parameterCache;
	
// 	/**
// 	 * @return GDT[]
// 	 */
// 	public function &gdoParameterCache() : array
// 	{
// 		if (!isset($this->parameterCache))
// 		{
// 			$this->parameterCache = [];
			
// 			foreach ($this->gdoParametersB() as $gdt)
// 			{
				
// 				if ($gdt->hasName())
// 				{
// 					$this->parameterCache[$gdt->name] = $gdt;
// 				}
// // 				else
// // 				{
// // 					$this->parameterCache[] = $gdt;
// // 				}
// 			}
// 		}
// 		return $this->parameterCache;
// 	}
	
// 	public function gdoParameter(string $name) : GDT
// 	{
// 		return $this->gdoParameterCache()[$name];
// 	}
	
// 	public function gdoParameterVar(string $name) : string
// 	{
// 		return $this->gdoParameter($name)->var;
// 	}
	
// 	public function gdoParameterValue(string $name) : string
// 	{
// 		return $this->gdoParameter($name)->getValue();
// 	}
	
// 	public function parameters(array $inputs, bool $throw=true) : self
// 	{
// 		$i = 0;
// 		/**
// 		 * @var GDT[] $positional
// 		 */
// 		$positional = [];
// 		/**
// 		 * @var GDT[] $namedional
// 		 */
// 		$namedional = [];
		
// 		foreach ($this->gdoParameterCache() as $gdt)
// 		{
// 			if ($gdt->isPositional())
// 			{
// 				$positional[] = $gdt;
// 				if ($gdt->hasName())
// 				{
// 					$namedional[$gdt->getName()] = $gdt;
// 				}
// 			}
// 			elseif ($gdt->hasName())
// 			{
// 				$namedional[$gdt->getName()] = $gdt;
// 			}
// 			elseif ($throw)
// 			{
// 				throw new GDO_Error('err_gdt_should_have_a_name', [$gdt->gdoShortName()]);
// 			}
// 		}

// 		foreach ($inputs as $key => $input)
// 		{
// 			if (is_numeric($key))
// 			{
// 				$positional[$i++]->input($input);
// 			}
// 			elseif (isset($namedional[$key]))
// 			{
// 				$namedional[$key]->input($input);
// 			}
// 			elseif ($throw)
// 			{
// 				throw new GDO_Error('err_gdt_should_have_a_name', [html($key)]);
// 			}
// 		}
		
// 		return $this;
// 	}
	
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
			return GDT_Error::make()->text('err_method_disabled');
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
			$ut = $user->getType();
// 			if (is_array($mt))
// 			{
// 				if (!in_array($ut, $mt))
// 				{
// 					return GDT_Error::responseWith(
// 						'err_user_type', [implode(' / ', $this->getUserType())]);
// 				}
// 			}
// 			else
			if ($ut !== $mt)
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
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		return GDT_Success::make()->titleRaw($this->getModuleName())->text($key, $args);
	}
	
	public function error(string $key, array $args = null, int $code = GDO_Exception::DEFAULT_ERROR_CODE, bool $log = true) : GDT
	{
		Application::setResponseCode($code);
		if ($log)
		{
			Logger::logError(ten($key, $args));
		}
		return GDT_Error::make()->titleRaw($this->getModuleName())->text($key, $args);
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
