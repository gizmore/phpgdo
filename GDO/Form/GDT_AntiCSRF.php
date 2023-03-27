<?php
namespace GDO\Form;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;
use GDO\Session\GDO_Session;
use GDO\User\GDO_User;
use GDO\Util\Random;

/**
 * GDT_Form CSRF protection.
 * Can optionally fallback to a static token. @TODO verify crypto.
 * This is useful in fileCached() MethodForm's.
 *
 * - Configure $expire
 *
 * @version 7.0.1
 * @since 1.0.0
 * @author gizmore
 * @see Cache
 * @see MethodForm
 */
class GDT_AntiCSRF extends GDT_String
{

	public const KEYLEN = 8;
	public const MAX_KEYS = 12;
	public string $token;
	public int $csrfExpire = 60 * 60;
	public bool $fixed = false;

	protected function __construct()
	{
		parent::__construct();
		$mod = Module_Form::instance();
		if ($mod->cfgXSRFMode() === 'fixed')
		{
			$this->fixed();
		}
		$this->csrfExpire($mod->cfgXSRFDuration());
		$this->token = $this->csrfToken();
	}

	###########
	### GDT ###
	###########

	public function fixed(bool $fixed = true): self
	{
		$this->fixed = $fixed;
		return $this;
	}

	public function csrfExpire(int $csrfExpire): self
	{
		$this->csrfExpire = $csrfExpire;
		return $this;
	}

	##############
	### Expire ###
	##############

	public function csrfToken()
	{
		if ($this->fixed || (!module_enabled('Session')))
		{
			return self::fixedToken();
		}

		$token = '';
		if (GDO_Session::instance())
		{
			$token = Random::randomKey(self::KEYLEN);
			$csrf = $this->loadCSRFTokens();
			$csrf[$token] = Application::$TIME;
			$this->saveCSRFTokens($csrf);
		}
		return $token;
	} # 1 hour is maybe sensible default.

	/**
	 * Calculate a fixed static token for a user.
	 *
	 * @TODO verify crypto
	 */
	public static function fixedToken(GDO_User $user = null): string
	{
		$user = $user ? $user : GDO_User::current();
		$time = 1337;
		$hash = sprintf('%s_%s_%s_%s_%s',
			GDO_SALT, $user->renderUserName(),
			GDO_SALT, $user->gdoVar('user_id'), $time);
		return substr(sha1($hash), 0, self::KEYLEN);
	}

	#############
	### Fixed ###
	#############

	private function loadCSRFTokens()
	{
		if ($csrf = GDO_Session::get('csrfs'))
		{
			$csrf = json_decode($csrf, true);
		}
		return $csrf ? $csrf : [];
	}

	private function saveCSRFTokens(array $csrf)
	{
		$count = count($csrf);
		if ($count > self::MAX_KEYS)
		{
			$csrf = array_slice($csrf, $count - self::MAX_KEYS, self::MAX_KEYS);
		}
		GDO_Session::set('csrfs', json_encode($csrf));
	}

	public function getDefaultName(): ?string { return 'xsrf'; }

	public function isHidden(): bool { return true; }

	#################
	### Construct ###
	#################

	public function isSerializable(): bool { return false; }

	public function getGDOData(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	###################
	### Load / Save ###
	###################

	public function plugVars(): array
	{
		return [];
	}

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (Module_Form::instance()->cfgXSRFMode() === 'off')
		{
			return true;
		}

		$headers = getallheaders();
		if (isset($headers['X-CSRF-TOKEN']))
		{
			$value = $headers['X-CSRF-TOKEN'];
		}

		$app = Application::$INSTANCE;
		if ($app->isCLI() || $app->isUnitTests())
		{
			return true;
		}

		if ($this->fixed || (!module_enabled('Session')))
		{
			if ($value === self::fixedToken())
			{
				return true;
			}
			return $this->error('err_csrf');
		}

		# Load tokens
		$csrf = $this->loadCSRFTokens();

		# Remove expired
		$needSave = false;
		foreach ($csrf as $token => $time)
		{
			if (Application::$TIME > ($time + $this->csrfExpire))
			{
				unset($csrf[$token]);
				$needSave = true;
			}
			else
			{
				break;
			}
		}

		# save tokens
		if ($needSave)
		{
			$this->saveCSRFTokens($csrf);
		}

		# Token not there
		if (!isset($csrf[$value]))
		{
			return $this->error('err_csrf');
		}

		# All fine
		return true;
	}

	################
	### Validate ###
	################

	public function renderForm(): string
	{
		return GDT_Template::php('Form', 'xsrf_html.php', ['field' => $this]);
	}

	##############
	### Render ###
	##############

	/**
	 * After success submit, remove the token.
	 */
	public function onSubmitted(): void
	{
		if (module_enabled('Session'))
		{
			$csrf = $this->loadCSRFTokens();
			$value = $this->getValue();
			unset($csrf[$value]);
			$this->saveCSRFTokens($csrf);
		}
	}

	##############
	### Events ###
	##############

	public function complex(): self { return $this->fixed(false); }

}
