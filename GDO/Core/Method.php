<?php
namespace GDO\Core;

use GDO\DB\Database;
use GDO\User\GDO_User;
use GDO\Util\Strings;
use GDO\UI\WithTitle;
use GDO\UI\WithDescription;
use GDO\Language\Trans;
use GDO\UI\GDT_Redirect;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Form;
use GDO\Date\Time;
use GDO\Util\Arrays;

/**
 * Abstract baseclass for all methods.
 * Checks permission.
 * Sets up SEO method meta data.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.1
 * @see GDT
 * @see GDO
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
	public function isCLI() : bool { return true; }
	public function isWeb() : bool { return true; }
	public function isAjax() : bool { return false; }
	/**
	 * Toggle if method can be trivially fuzz-tested. defaults to yes.
	 */
	public function isTrivial() : bool { return true; }
	public function isLocking() : bool { return false; }
	public function isUserRequired() : bool { return false; }
	public function isGuestAllowed() : bool { return Module_Core::instance()->cfgAllowGuests(); }
	public function isTransactional() : bool { return Application::$INSTANCE->verb === GDT_Form::POST; }
	public function isAlwaysAllowed() : bool { return false; }
	public function isAlwaysTransactional() : bool { return false; }
	public function isSavingLastUrl() : bool { return true; }
	public function isShownInSitemap() : bool { return true; }
	public function getUserType() : ?string { return null; }
	public function isIndexed() : bool { return true; }
	public function isSidebarEnabled() : bool { return true; }
	
	# events
	public function onMethodInit() {}
	public function onRenderTabs() : void {}
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
	
	public function getAutoButton(array $keys=null) : ?string
	{
		$first = null;
		$keys = Arrays::arrayed($keys);
		foreach ($this->gdoParameterCache() as $key => $gdt)
		{
			if ($gdt instanceof GDT_Submit)
			{
				if (in_array($key, $keys, true))
				{
					Application::$INSTANCE->verb(GDT_Form::POST);
					return $key;
				}
				if (!$first)
				{
					$first = $gdt->getName();
				}
			}
		}
		
		if ($first)
		{
			Application::$INSTANCE->verb(GDT_Form::POST);
		}
		
		return $first;
	}
	
	
	############
	### HREF ###
	############
	public function href(string $append='') : string
	{
		return $this->getModule()->href($this->getMethodName(), $append);
	}
	
	public function hrefNoSEO(string $append='') : string
	{
		return $this->getModule()->hrefNoSEO($this->getMethodName(), $append);
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
			
			if (!($module = ModuleLoader::instance()->getModule($moduleName, false, $throw)))
			{
// 				if ($throw)
// 				{
// 					throw new GDO_Error('err_unknown_module', [html($moduleName)]);
// 				}
				return null;
			}
			
			$methodName = Strings::substrFrom($alias, '.', t('none'));
			if ($method = $module->getMethod($methodName, false))
			{
				return $method;
			}
			if ($throw)
			{
				throw new GDO_NoSuchMethodError($module, $methodName);
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
// 		if ($user->isSystem())
// 		{
// 			# This, f.e., is needed for the gdo_adm.sh configure installer.
// 			return true;
// 		}
		
		if (!($this->isEnabled()))
		{
			return $this->error('err_method_disabled', [$this->getModuleName(), $this->getMethodName()], 403);
		}
		
		if ( ($this->isUserRequired()) && (!$this->isGuestAllowed()) && (!$user->isMember()) )
		{
			$hrefAuth = href('Login', 'Form', "&_backto=".urlencode($_SERVER['REQUEST_URI']));
			return $this->error('err_members_only', [$hrefAuth]);
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
				$hrefAuth = href('Login', 'Form', "&_backto=".urlencode($_SERVER['REQUEST_URI']));
				return $this->error('err_members_only', [$hrefAuth]);
			}
		}
		
		if ($mt = $this->getUserType())
		{
			if (!$user->isAdmin())
			{
				$mt = explode(',', $mt);
				$ut = $user->getType();
				if (!in_array($ut, $mt, true))
				{
					return $this->error('err_user_type', [Arrays::implodeHuman($mt, 'or')]);
				}
			}
		}
		
		if ($mp = $this->getPermission())
		{
			if (!$user->isAdmin())
			{
				$mp = explode(',', $mp);
				$has = false;
				foreach ($mp as $permission)
				{
					if ($user->hasPermission($permission))
					{
						$has = true;
						break;
					}
				}
				if (!$has)
				{
					return $this->error('err_permission_required');
				}
			}
		}
		
// 		if (!$user->isAdmin())
// 		{
			if (!$this->hasPermission($user))
			{
				return $this->error('err_permission_required');
			}
// 		}
		
		return true;
	}
	
	/**
	 * Test permissions and execute method.
	 */
	public function exec()
	{
// 		$user = GDO_User::current();
		
// 		if (true !== ($error = $this->checkPermission($user)))
// 		{
// 			return $error;
// 		}
		
		return $this->execWrap();
	}
	
// 	public function execMethod()
// 	{
// 		return $this->execWrap();
// 	}
	
	/**
	 * Detect if we should start a transaction. # @TODO only mark DB transaction ready / lazily
	 * This happens when it's generally transaction worthy method (isTransactional())
	 * And if the REQUEST VERB is POST.
	 * Another option is: isAlwaysTransactional()
	 */
	public function transactional() : bool
	{
		if (Application::instance()->isInstall())
		{
			return false;
		}
		return
			($this->isAlwaysTransactional()) ||
			($this->isTransactional() &&
				(Application::$INSTANCE->verb === GDT_Form::POST) );
	}
	
	public function locking() : bool
	{
		return $this->isLocking() && $this->transactional();
	}
	
	/**
	 * Wrap execution in transaction if desired from method.
	 */
	public function execWrap()
	{
		$response = $this->executeWithInit();
		return $response;
	}
	
	public function executeWithInputs(array $inputs=null, bool $checkPermission=true)
	{
		$this->inputs = $inputs;
		return $this->executeWithInit($checkPermission);
	}
	
	/**
	 * Execute this method with all hooks.
	 * Quite a long method.
	 */
	public function executeWithInit(bool $checkPermission=true)
	{
		$db = Database::instance();
// 		$app = Application::$INSTANCE;
		$response = GDT_Response::make();
		$transactional = false;
		try
		{
			if ($result = $this->onMethodInit())
			{
				$response->addField($result);
			}
			
			# 0) Init
			$this->applyInput();
			
			$user = GDO_User::current();
			
			if ($checkPermission)
			{
				if (true !== ($error = $this->checkPermission($user)))
				{
					return $error;
				}
			}
			
			if (Application::isError())
			{
				return $response;
			}
			
			# 1) Start the transaction
			$this->lock();
			if ($this->transactional())
			{
				$db->transactionBegin();
				$transactional = true;
			}

			# 2) Before execute
			$this->beforeExecute();
			$result = GDT_Hook::callHook('BeforeExecute', $this, $response);
			if ($result)
			{
				$response->addField($result);
			}
			if ($response->hasError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			# 3) Build top response tabs
			if (Application::$INSTANCE->isHTML())
			{
				$this->onRenderTabs();
			}
			
			# 4) Execute
			if ($result = $this->executeB())
			{
				if ($result->hasError())
				{
					$response->code(GDO_Error::DEFAULT_ERROR_CODE);
					$response->errorRaw($result->renderError());
				}
				$response->addField($result);
			}
			
			# 4b) Error
			if (Application::isError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			# 5) After execute
			$this->afterExecute();
			$result = GDT_Hook::callHook('AfterExecute', $this, $response);
			if ($result)
			{
				$response->addField($result);
			}
			if ($response->hasError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
				return $response;
			}
			
			# 5b)
			$this->setupSEO();

			# 6) Commit transaction
			if ($transactional)
			{
				$db->transactionEnd();
			}
			
			return $response;
		}
		catch (GDO_ErrorFatal $e)
		{
			if ($transactional)
			{
				$db->transactionRollback();
			}
			throw $e;
		}
		catch (GDO_Error $e)
		{
			if ($transactional)
			{
				$db->transactionRollback();
			}
			return $this->error('error', [$e->getMessage()]);
		}
		catch (GDO_ArgException $e)
		{
			if ($transactional)
			{
				$db->transactionRollback();
			}
			return $this->error('error', [$e->getMessage()]);
		}
		catch (GDO_RedirectError $e)
		{
			return GDT_Redirect::make()->redirectErrorRaw($e->getMessage())->href($e->href);
		}
		catch (GDO_PermissionException $e)
		{
			if ($transactional)
			{
				$db->transactionRollback();
			}
			Logger::logException($e);
			return $this->error('error', [$e->getMessage()]);
		}
		catch (\Throwable $e)
		{
			if ($transactional)
			{
				$db->transactionRollback();
			}
			throw $e;
		}
		finally
		{
// 			foreach ($this->gdoParameterCache() as $gdt)
// 			{
// 				$gdt->inputs(null);
// 			}
			$this->unlock();
		}
	}
	
	############
	### Lock ###
	############
	private bool $locked = false;
	
	private function lockKey() : string
	{
		$user = GDO_User::current();
		$lock = GDO_SITENAME . "_USERLOCK_{$user->getID()}";
		return $lock;
	}
	
	private function lock() : bool
	{
		$user = GDO_User::current();
		if ( (!module_enabled('Session')) ||
			 (!$this->isLocking()) ||
			 (!$user->isPersisted()) )
		{
			return false;
		}
		$lock = $this->lockKey();
		if (Database::instance()->lock($lock))
		{
			$this->locked = true;
		}
		return $this->locked;
	}
	
	private function unlock() : bool
	{
		if (!$this->locked)
		{
			return true;
		}
		$lock = $this->lockKey();
		if (Database::instance()->unlock($lock))
		{
			$this->locked = false;
		}
		return !$this->locked;
	}
	
	###########
	### SEO ###
	###########
	public function setupSEO()
	{
		# SEO
		$description = $this->getMethodDescription();
		Website::setTitle($this->getMethodTitle());
		Website::addMeta(['keywords', $this->getMethodKeywords(), 'name']);
		Website::addMeta(['description', $description, 'name']);
		Website::addMeta(['og:description', $description, 'property']);
		
		# Store last URL in session
		if ( ($this->isSavingLastUrl()) &&
			(Application::$INSTANCE->isWebserver()) )
		{
			$this->storeLastURL();
			$this->storeLastActivity();
		}
	}
	
	public function getMethodTitle() : string
	{
		$key = sprintf('mt_%s_%s', $this->getModuleName(), $this->getMethodName());
		$key = strtolower($key);
		return Trans::hasKey($key) ? t($key) : GDT::EMPTY_STRING;
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
		$key = strtolower($key);
		if (Trans::hasKey($key))
		{
			$keywords[] = t($key);
		}
		return implode(', ', $keywords);
	}
	
	##################
	### Statistics ###
	##################
	/**
	 * Store last url if they was reading the website.
	 */
	private function storeLastURL() : void
	{
		$app = Application::$INSTANCE;
		$user = GDO_User::current();
		if ( ($app->verb === GDT_Form::GET) &&
			($app->isWebserver())  &&
			($app->isHTML()) &&
			(!$app->isAjax()) &&
			($user->isPersisted()) )
		{
			$user->saveSettingVar('User', 'last_url', $_SERVER['REQUEST_URI']);
		}
	}

	/**
	 * Update user last activity timestamp, for persisted users/guests.
	 * Basically only store POST requests to non-ajax methods. And exceptions.
	 */
	private function storeLastActivity() : void
	{
		if (Application::$INSTANCE->verb === GDT_Form::POST)
		{
			$user = GDO_User::current();
			if ($user->isPersisted())
			{
				$time = Application::$TIME;
				$time -= $time % $user->settingValue('Date', 'activity_accuracy');
				$date = Time::getDate($time);
				$user->saveSettingVar('User', 'last_activity', $date);
			}
		}
	}

	#############
	### Input ###
	#############
	/**
	 * Get plug variables.
	 * @return [string[string]]
	 */
	public function plugVars() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
	public function plugUserID() : string
	{
		return '2'; # gizmore
	}
	
	public function plugUser() : GDO_User
	{
		return GDO_User::findById($this->plugUserID());
	}
	
	public function appliedInputs(array $inputs) : self
	{
		$this->inputs($inputs);
		$this->applyInput();
		return $this;
	}
	
	protected function applyInput(): void
	{
		$inputs = $this->getInputs();
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$gdt->inputs($inputs);
		}
	}
	
	#############
	### Error ###
	#############
	public function message(string $key, array $args = null, int $code = 200, bool $log = true) : GDT
	{
		$titleRaw = $this->getModule()->gdoHumanName();
		return Website::message($titleRaw, $key, $args, $log, $code);
	}
	
	public function error(string $key, array $args = null, int $code = GDO_Exception::DEFAULT_ERROR_CODE, bool $log = true) : GDT
	{
		$titleRaw = $this->getModule()->gdoHumanName();
		return Website::error($titleRaw, $key, $args, $log, $code);
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
		$href = GDT_Redirect::hrefBack($default);
		return $this->redirect($href);
	}
	
	public function redirectMessage(string $key, array $args = null, string $href=null) : GDT_Redirect
	{
		$href = $href ? $href : GDT_Redirect::hrefBack();
		return GDT_Redirect::make()->href($href)
			->redirectMessage($key, $args);
	}
	
	public function redirectError(string $key, array $args = null, string $href=null) : GDT_Redirect
	{
		$href = $href ? $href : GDT_Redirect::hrefBack();
		return GDT_Redirect::make()->href($href)
			->redirectError($key, $args);
	}
	
	################
	### Template ###
	################
	public function templatePHP(string $path, array $tVars=null) : GDT_Template
	{
		return GDT_Template::make()->template($this->getModuleName(), $path, $tVars);
	}

	public function tempPath(string $path = ''): string
	{
		$module = $this->getModule();
		return $module->tempPath($this->getMethodName().'/'.$path);
	}

}
