<?php
namespace GDO\Core;

use GDO\DB\Database;
use GDO\UI\GDT_Error;
use GDO\UI\GDT_Success;
use GDO\User\GDO_User;
use GDO\Util\Strings;

/**
 * Abstract baseclass for all methods.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.1
 */
abstract class Method
{
	use WithModule;
	use WithInstance;

	################
	### Override ###
	################
	public abstract function execute() : GDT;
	public function isTrivial() { return true; }
	public function isTransactional() : bool { return false; }
	public function isAlwaysTransactional() : bool { return false; }
	public function onInit() : ?GDT { return null; }
	public function beforeExecute() : void {}
	public function afterExecute() : void {}
	public function getPermission() : ?string { return null; }
	
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
	/**
	 * @return GDT[]
	 */
	public function gdoParameters() : array
	{
		return [];
	}
	
	protected function gdoParametersB() : array
	{
		return $this->gdoParameters();
	}
	
	/**
	 * @var GDT[]
	 */
	private array $parameterCache;
	
	/**
	 * @return GDT[]
	 */
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			foreach ($this->gdoParametersB() as $gdt)
			{
				if ($gdt->hasName())
				{
					$this->parameterCache[$gdt->name] = $gdt;
				}
// 				else
// 				{
// 					$this->parameterCache[] = $gdt;
// 				}
			}
		}
		return $this->parameterCache;
	}
	
	public function gdoParameter($name) : GDT
	{
		return $this->gdoParameterCache()[$name];
	}
	
	public function gdoParameterVar($name) : string
	{
		return $this->gdoParameter($name)->var;
	}
	
	public function gdoParameterValue($name) : string
	{
		return $this->gdoParameter($name)->getValue();
	}
	
	public function parameters(array $inputs, bool $throw=true) : self
	{
		$i = 0;
		/**
		 * @var GDT[] $positional
		 */
		$positional = [];
		/**
		 * @var GDT[] $namedional
		 */
		$namedional = [];
		
		foreach ($this->gdoParameterCache() as $gdt)
		{
			if ($gdt->isPositional())
			{
				$positional[] = $gdt;
				if ($gdt->hasName())
				{
					$namedional[$gdt->getName()] = $gdt;
				}
			}
			elseif ($gdt->hasName())
			{
				$namedional[$gdt->getName()] = $gdt;
			}
			elseif ($throw)
			{
				throw new GDO_Error('err_gdt_should_have_a_name', [$gdt->gdoShortName()]);
			}
		}

		foreach ($inputs as $key => $input)
		{
			if (is_numeric($key))
			{
				$positional[$i++]->input($input);
			}
			elseif (isset($namedional[$key]))
			{
				$namedional[$key]->input($input);
			}
			elseif ($throw)
			{
				throw new GDO_Error('err_gdt_should_have_a_name', [html($key)]);
			}
		}
		
		return $this;
	}
	
	############
	### Exec ###
	############
	/**
	 * Test permissions and execute method.
	 */
	public function exec()
	{
		if ($this->isAjax())
		{
			$_REQUEST['_ajax'] = '1';
		}
		
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
			if (module_enabled('Register') && Module_Register::instance()->cfgGuestSignup())
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
			if (is_array($mt))
			{
				if (!in_array($ut, $mt))
				{
					return GDT_Error::responseWith(
						'err_user_type', [implode(' / ', $this->getUserType())]);
				}
			}
			elseif ($ut !== $mt)
			{
				return GDT_Error::make()->text('err_user_type', [$this->getUserType()]);
			}
		}
		
		if ( ($permission = $this->getPermission()) && (!$user->hasPermission($permission)) )
		{
			return GDT_Error::make()->text('err_permission_required', [t('perm_'.$permission)]);
		}
		
		if (true !== ($error = $this->hasPermission($user)))
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
		$response = $this->executeWithInit();
		
		if ( (!$response) || (!$response->isError()) )
		{
			$this->setupSEO();
		}
		
		return $response;
	}
	
	public function setupSEO()
	{
		# SEO
		Website::setTitle($this->getTitle());
		Website::addMeta(['keywords', $this->getKeywords(), 'name']);
		Website::addMeta(['description', $this->getDescription(), 'name']);
		
		# Store last URL in session
		$this->storeLastURL();
		
		# Store last activity in user
		$this->storeLastActivity();
	}
	
	public function executeWithInit()
	{
		$db = Database::instance();
		$transactional = $this->transactional();
		try
		{
			# Wrap transaction start
			if ($transactional)
			{
				$db->transactionBegin();
			}
			
			# Init method
			$response = GDT_Response::make();
			
			$this->inited = false;
			$response->addField($this->onInit());
			
			if (Application::isError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			# Exec 1.before - 2.execute - 3.after
			GDT_Hook::callHook('BeforeExecute', $this, $response);
			
			$response = GDT_Response::make();
			if ($response->hasError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			$response->addField($this->beforeExecute());
			if ($response->hasError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			$response->addField($this->execute());
			
			if (Application::isSuccess())
			{
				$response->addField($this->afterExecute());
				GDT_Hook::callHook('AfterExecute', $this, $response);
				if ($transactional)
				{
					$db->transactionEnd();
				}
			}
			
			# Wrap transaction end
			else if ($transactional)
			{
				$db->transactionRollback();
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
