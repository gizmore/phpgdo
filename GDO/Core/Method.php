<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Date\Time;
use GDO\DB\Database;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Language\Trans;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Redirect;
use GDO\UI\WithDescription;
use GDO\UI\WithTitle;
use GDO\User\GDO_User;
use GDO\Util\Arrays;
use GDO\Util\Strings;
use Throwable;

/**
 * Abstract baseclass for all methods.
 * Checks permission.
 * Sets up SEO method meta data.
 *
 * @version 7.0.3
 * @since 3.0.1
 * @author gizmore
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

	/**
	 * @var string[]
	 */
	public static array $CLI_ALIASES = [];

	################
	### Override ###
	################
	# execution
	public string $button;

	private bool $locked = false;

	/**
	 * Get a method by cli convention. Aliases first, then module DOT method.
	 */
	public static function getMethod(string $alias): ?self
	{
		$alias = strtolower($alias);
		if (isset(self::$CLI_ALIASES[$alias]))
		{
			$klass = self::$CLI_ALIASES[$alias];
			return call_user_func([$klass, 'make']);
		}
		else
		{
			$moduleName = Strings::substrTo($alias, '.', $alias);

			if (!($module = ModuleLoader::instance()->getModule($moduleName, false)))
			{
				return null;
			}
			$methodName = Strings::substrFrom($alias, '.', t('none'));
			if ($method = $module->getMethod($methodName, false))
			{
				return $method;
			}
		}
		return null;
	}

	public static function make(): static
	{
		return new static();
	}

	public function getCLITrigger(): string
	{
		return strtolower("{$this->getModule()->getCLITrigger()}.{$this->getMethodName()}");
	}

	public function getMethodName(): string
	{
		return $this->gdoShortName();

	}

	public function isCLI(): bool { return true; }

	# toggles

	public function isAjax(): bool { return false; }

//	public function isWeb() : bool { return true; }

	/**
	 * Toggle if method can be trivially fuzz-tested. defaults to yes.
	 */
	public function isTrivial(): bool { return true; }

	public function isAlwaysAllowed(): bool { return false; }

	public function isSavingLastUrl(): bool { return true; }

	public function isShownInSitemap(): bool { return true; }

	public function isHiddenMethod() { return false; }


	public function isIndexed(): bool { return true; }

	public function isSidebarEnabled(): bool { return true; }

	public function getAutoButton(array $keys = null): ?string
	{
		$first = null;
		$keys = Arrays::arrayed($keys);
		foreach ($this->gdoParameterCache() as $key => $gdt)
		{
			if ($gdt instanceof GDT_Submit)
			{
				if (in_array($key, $keys, true))
				{
					return $key;
				}
				$first ??= $gdt->getName();
			}
		}
		return $first;
	}

	public function getID(): ?string
	{
		return $this->getName();
	}

	public function getName(): ?string
	{
		return $this->gdoClassName();
	}

	public function renderName(): string
	{
		return $this->getName();
	}

	public function hrefNoSEO(string $append = ''): string
	{
		return $this->getModule()->hrefNoSEO($this->getMethodName(), $append);
	}

	/**
	 * Test permissions and execute method.
	 */
	public function exec(): GDT
	{
		return $this->execWrap();
	}

	/**
	 * Wrap execution in transaction if desired from method.
	 */
	public function execWrap(): GDT
	{
		return $this->executeWithInit();
	}

	/**
	 * Execute this method with all hooks.
	 */
	public function executeWithInit(bool $checkPermission = true): GDT
	{
		global $me;
		$me = $this;
		$db = Database::instance();
		$this->locked = false;
		$transactional = false;
		$response = GDT_Response::make();
		try
		{
			$this->applyInput();

			# 0) Init
			if ($result = $this->onMethodInit())
			{
				$response->addField($result);
			}
			if (Application::isError())
			{
				return $response;
			}
			$this->applyInput();

			$user = GDO_User::current();
			if ($checkPermission)
			{
				if ($error = $this->checkPermission($user))
				{
					return $error;
				}
			}

			# 1) Start the transaction
			if ($this->transactional())
			{
				$transactional = true;
				$db->transactionBegin();
			}
//			$this->lock();

			# 2) Before execute
			$this->beforeExecute();
			$result = GDT_Hook::callHook('BeforeExecute', $this, $response);
			if ($result)
			{
				$response->addField($result);
			}
			if ($response->hasError())
			{
//				if ($transactional)
//				{
//					$db->transactionRollback();
//				}
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
					$response->code(GDO_Exception::DEFAULT_ERROR_CODE);
					$response->errorRaw($result->renderError());
				}
				$response->addField($result);
			}

			# 4b) Error
			if (Application::isError())
			{
//				if ($transactional)
//				{
//					$db->transactionRollback();
//				}
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
//				if ($transactional)
//				{
//					$db->transactionRollback();
//				}
				return $response;
			}

			# 5b)
			if (Application::$INSTANCE->isWebserver())
			{
				$this->setupSEO();
			}

			# 5c)
			$this->storeLastActivity();

			# 6) Commit transaction
			if ($transactional)
			{
				$db->transactionEnd();
			}

			return $response;
		}
//		catch (GDO_Error $e)
//		{
////			if ($transactional)
////			{
////				$db->transactionRollback();
////			}
////			# In CLI/Chat we need to bubble up.
////			if (!Application::$INSTANCE->isWebserver())
////			{
////				throw $e;
////			}
//			return $this->error('error', [$e->getMessage()]);
//		}
//		catch (GDO_ArgException $e)
//		{
//			if ($transactional)
//			{
//				$db->transactionRollback();
//			}
//			return $this->error('error', [$e->getMessage()]);
//		}
		catch (GDO_RedirectError $e)
		{
//			if ($transactional)
//			{
//				$db->transactionRollback();
//			}
			return GDT_Redirect::make()->redirectError($e->key, $e->args)->href($e->href);
		}
//		catch (GDO_PermissionException $e)
//		{
//			if ($transactional)
//			{
//				$db->transactionRollback();
//			}
//			Logger::logException($e);
//			return $this->error('error', [$e->getMessage()]);
//		}
//		catch (Throwable $e)
//		{
//			return $this->error('error', [$e->getMessage()]);
//		}
		finally
		{
			if (Application::isError())
			{
				if ($transactional)
				{
					$db->transactionRollback();
				}
			}
//			$this->unlock();
		}
	}

	public function onMethodInit(): ?GDT { return null; }

	# events

	protected function applyInput(): void
	{
		$inputs = $this->getInputs();
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$gdt->inputs($inputs);
		}
	}

	/**
	 * Check permissions.
	 *
	 * @note return "null" for no errors!
	 */
	public function checkPermission(GDO_User $user, bool $silent=false): ?GDT
	{
		$error = '';
		$args = [];
		if (!$this->checkPermissionB($user, $error, $args))
		{
			if (!$silent)
			{
				return $this->error($error, $args);
			}
			return GDT_Response::make();
		}
		return null;
	}

	private function checkPermissionB(GDO_User $user, string &$error, array &$args): bool
	{
		if (!($this->isEnabled()))
		{
			$error = 'err_method_disabled';
			$args = [$this->getModuleName(), $this->getMethodName()];
			return false;
		}

		if (($this->isUserRequired()) && (!$this->isGuestAllowed()) && (!$user->isMember()))
		{
			$hrefAuth = href('Login', 'Form', '&_backto=' . urlencode($_SERVER['REQUEST_URI']));
			$error = 'err_members_only';
			$args = [$hrefAuth];
			return false;
		}

		if (($this->isUserRequired()) && (!$user->isUser()))
		{
			if (GDO_Module::config_var('Register', 'guest_signup', '0'))
			{
				$hrefGuest = href('Register', 'Guest', '&_backto=' . urlencode($_SERVER['REQUEST_URI']));
				$error = 'err_user_required';
				$args = [$hrefGuest];
			}
			else
			{
				$hrefAuth = href('Login', 'Form', '&_backto=' . urlencode($_SERVER['REQUEST_URI']));
				$error = 'err_members_only';
				$args = [$hrefAuth];
			}
			return false;
		}

		if ($mt = $this->getUserType())
		{
			if (!$user->isAdmin())
			{
				$mt = explode(',', $mt);
				$ut = $user->getType();
				if (!in_array($ut, $mt, true))
				{
					$error = 'err_user_type';
					$args = [Arrays::implodeHuman($mt, 'or')];
					return false;
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
					$error = 'err_permission_required';
					return false;
				}
			}
		}

		if (!$this->hasPermission($user))
		{
			$error = 'err_permission_required';
			return false;
		}

		return true;
	}

	public function isEnabled(): string { return $this->getModule()->isEnabled(); }

	public function error(string $key, array $args = null, int $code = GDO_Exception::DEFAULT_ERROR_CODE, bool $log = true): GDT
	{
		$titleRaw = $this->getModule()->gdoHumanName();
		return Website::error($titleRaw, $key, $args, $log, $code);
	}

	###################
	### Alias Cache ###
	###################

	public function isUserRequired(): bool { return false; }

//	public static function addCLIAlias(string $alias, string $className) : void
//	{
//		self::$CLI_ALIASES[$alias] = $className;
//	}

	public function isGuestAllowed(): string { return Module_Core::instance()->cfgAllowGuests(); }


	############
	### HREF ###
	############

	public function getUserType(): ?string { return null; }

	public function getPermission(): ?string { return null; }

	public function hasPermission(GDO_User $user): bool { return true; }

	###################
	### Instanciate ###
	###################

	private function lock(): bool
	{
		$user = GDO_User::current();
		if (
			(!module_enabled('Session')) ||
			(!$this->isLocking()) ||
			(!$user->isPersisted())
		)
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

	############
	### Exec ###
	############

	public function isLocking(): bool { return false; }

	private function lockKey(): string
	{
		$user = GDO_User::current();
		return GDO_SITENAME . "_USERLOCK_{$user->getID()}";
	}

	/**
	 * Detect if we should start a transaction. # @TODO only mark DB transaction ready / lazily
	 * This happens when it's generally transaction worthy method (isTransactional())
	 * And if the REQUEST VERB is POST.
	 * Another option is: isAlwaysTransactional()
	 */
	public function transactional(): bool
	{
		if (Application::$INSTANCE->isWebserver())
		{
			return
				($this->isAlwaysTransactional()) ||
				($this->isTransactional() &&
					(Application::$INSTANCE->verb === GDT_Form::POST));
		}
		return false;
	}

	public function isAlwaysTransactional(): bool { return false; }

	public function isTransactional(): bool { return Application::$INSTANCE->verb === GDT_Form::POST; }

	public function beforeExecute(): void {}

	public function onRenderTabs(): void {}

	protected function executeB(): GDT
	{
		return $this->execute();
	}

	############
	### Lock ###
	############

	abstract public function execute(): GDT;

	public function afterExecute(): void {}

	public function setupSEO(): void
	{
		# SEO
		$description = $this->getMethodDescription();
		Website::setTitle($this->getMethodTitle());
		Website::addMeta(['keywords', $this->getMethodKeywords(), 'name']);
		Website::addMeta(['description', $description, 'name']);
		Website::addMeta(['og:description', $description, 'property']);
	}

	public function getMethodDescription(): string
	{
		$key = sprintf('md_%s_%s', $this->getModuleName(), $this->getMethodName());
		$key = strtolower($key);
		return Trans::hasKey($key) ? t($key) : $this->getMethodTitle();
	}

	###########
	### SEO ###
	###########

	public function getMethodTitle(): string
	{
		$key = sprintf('mt_%s_%s', $this->getModuleName(), $this->getMethodName());
		$key = strtolower($key);
		return t($key);
	}

	public function getMethodKeywords(): string
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

	/**
	 * Update user last activity timestamp, for persisted users/guests.
	 * Basically only store POST requests to non-ajax methods. And exceptions.
	 */
	private function storeLastActivity(): void
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

	public function href(string $append = ''): string
	{
		return $this->getModule()->href($this->getMethodName(), $append);
	}

	private function unlock(): bool
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

	##################
	### Statistics ###
	##################

	public function locking(): bool
	{
		return $this->isLocking() && $this->transactional();
	}

	#############
	### Input ###
	#############

	public function executeWithInputs(array $inputs = null, bool $checkPermission = true): GDT
	{
		$this->inputs = $inputs;
		return $this->executeWithInit($checkPermission);
	}

	/**
	 * Execute this method without any checks or events.
	 * Used when invoking methods inside other methods.
	 */
	public function execWithInputs(array $inputs = null): GDT
	{
		$this->inputs = $inputs;
		$this->applyInput();
		return $this->executeB();
	}

	/**
	 * Return a @see Website compatible meta data, for an image in teams links.
	 */
	public function seoMetaImage(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	/**
	 * Get plug variables.
	 */
	public function plugVars(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	public function plugUser(): GDO_User
	{
		return GDO_User::findById($this->plugUserID());
	}

	#############
	### Error ###
	#############

	public function plugUserID(): string
	{
		return '2'; # gizmore
	}

	public function appliedInputs(array $inputs): self
	{
		$this->inputs($inputs);
		$this->applyInput();
		return $this;
	}

	################
	### Redirect ###
	################

	public function message(string $key, array $args = null, int $code = 200, bool $log = true): GDT
	{
		$titleRaw = $this->getModule()->gdoHumanName();
		return Website::message($titleRaw, $key, $args, $log, $code);
	}

	public function redirectBack(string $default = null): GDT_Redirect
	{
		$href = GDT_Redirect::hrefBack($default);
		return $this->redirect($href);
	}

	public function redirect(string $href): GDT_Redirect
	{
		return GDT_Redirect::make()->href($href);
	}

	public function redirectMessage(string $key, array $args = null, string $href = null): GDT_Redirect
	{
		$href = $href ?: GDT_Redirect::hrefBack();
		$redirect = GDT_Redirect::make()->href($href)
			->redirectMessage($key, $args);
		GDT_Page::instance()->topResponse()->addField($redirect);
		return $redirect;
	}

	################
	### Template ###
	################

	public function redirectError(string $key, array $args = null, string $href = null): GDT_Redirect
	{
		$href = $href ?: GDT_Redirect::hrefBack();
		$redirect = GDT_Redirect::make()->href($href)
			->redirectError($key, $args);
		GDT_Page::instance()->topResponse()->
		addField($redirect);
		return $redirect;
	}

	public function templatePHP(string $path, array $tVars = null): GDT_Template
	{
		return GDT_Template::make()->template(
			$this->getModuleName(), $path, $tVars);
	}

	##################
	### CLI Button ###
	##################

	public function tempPath(string $path = ''): string
	{
		$module = $this->getModule();
		return $module->tempPath($this->getMethodName() . '/' . $path);
	}

	public function cliButton(string $button): self
	{
		$this->button = $button;
		$this->addInput($button, '1');
		return $this;
	}

}
