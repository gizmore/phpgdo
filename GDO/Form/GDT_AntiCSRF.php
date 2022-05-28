<?php
namespace GDO\Form;

use GDO\Session\GDO_Session;
use GDO\Util\Random;
use GDO\Core\Application;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;

/**
 * GDT_Form CSRF protection.
 * Can optionally fallback to a static token. @TODO verify crypto.
 * This is useful in fileCached() MethodForm's.
 * 
 * - Configure $expire 
 * 
 * @see Cache
 * @see MethodForm
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 1.0.0
 */
class GDT_AntiCSRF extends GDT_Hidden
{
    const KEYLEN = 6;
    const MAX_KEYS = 12;
    
	public function getDefaultName() : string { return 'xsrf'; }

	###########
	### GDT ###
	###########
	public function getGDOData() : ?array
	{
	    # Override GDT_Hidden with null data.
	}
	
	##############
	### Expire ###
	##############
	public int $csrfExpire = 60 * 30; # 0.5 hours is a sensible default.
	public function csrfExpire(int $csrfExpire) : self
	{
		$this->csrfExpire = $csrfExpire;
		return $this;
	}
	
	#############
	### Fixed ###
	#############
	public bool $fixed = false;
	public function complex() : self { return $this->fixed(false); }
	public function fixed(bool $fixed=true) : self
	{
	    $this->fixed = $fixed;
	    return $this;
	}
	
	/**
	 * Calculate a fixed static token for a user.
	 * @TODO verify crypto
	 * @return string
	 */
	public static function fixedToken(GDO_User $user=null)
	{
	    $user = $user ? $user : GDO_User::current();
// 	    $time = Application::$TIME;
// 	    $time = $time - ($time % $this->csrfExpire);
// 	    $time = date('YmdHis', $time);
	    $time = 1337;
	    $hash = sprintf('%s_%s_%s_%s_%s',
	        GDO_SALT, $user->renderUserName(),
            $user->getVar('user_email'),
	        $user->getVar('user_password'), $time);
	    return substr(sha1($hash), 0, self::KEYLEN);
	}
	
	#################
	### Construct ###
	#################
	public function csrfToken()
	{
	    if ($this->fixed)
	    {
	        return self::fixedToken();
	    }
	    
	    $token = '';
	    if (module_enabled('Session'))
	    {
			if (GDO_Session::instance())
			{
			    $token = Random::randomKey(self::KEYLEN);
			    $csrf = $this->loadCSRFTokens();
			    $csrf[$token] = Application::$TIME;
			    $this->saveCSRFTokens($csrf);
			}
	    }
		return $token;
	}
	
	###################
	### Load / Save ###
	###################
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
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
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
	    
	    # No session, no token
	    if (!class_exists('GDO\\Session\\GDO_Session', false))
		{
			return true;
// 			return $this->error('err_session_required');
		}
		
		if ($this->fixed)
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
		foreach ($csrf as $token => $time)
		{
		    if (Application::$TIME > ($time + $this->csrfExpire))
		    {
		        unset($csrf[$token]);
		    }
		}
		
		# Token not there
		if (!isset($csrf[$value]))
		{
			return $this->error('err_csrf');
		}

		# Remove used token
		unset($csrf[$value]);
		$this->saveCSRFTokens($csrf);
		
		# All fine
		return true;
	}

	##############
	### Render ###
	##############
	public function renderForm() : string
	{
		return GDT_Template::php('Form', 'xsrf_html.php', ['field'=>$this]);
	}
	
// 	public function jsonFormValue()
// 	{
// 		return $this->csrfToken();
// 	}
	
}
