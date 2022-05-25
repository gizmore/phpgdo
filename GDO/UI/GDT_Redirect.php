<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\Application;
use GDO\Session\GDO_Session;
use GDO\Core\Logger;

/**
 * A redirect.
 * Renders <script> in ajax mode.
 * Sets headers in html and displays a redirect message.
 * 
 * @TODO rename html to http rendering?
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.11.5
 */
final class GDT_Redirect extends GDT
{
	use WithHREF;
	
	public static bool $REDIRECTED = false; # Only once
	
	############
	### Back ###
	############
	public function back() : self
	{
		return $this->href(self::hrefBack());
	}
	
	/**
	 * Try to get a referrer URL for hrefBack.
	 * @param string $default
	 * @return string
	 */
	public static function hrefBack($default=null)
	{
		if (Application::instance()->isCLI())
		{
			return $default ? $default : hrefDefault();
		}
		
		$sess = null;
		if (class_exists('GDO\\Session\\GDO_Session', false))
		{
			$sess = GDO_Session::instance();
		}
		
		if ( (!$sess) || (!($url = $sess->getLastURL())) )
		{
			$url = isset($_SERVER['HTTP_REFERER']) ?
			$_SERVER['HTTP_REFERER'] :
			($default ? $default : hrefDefault());
		}
		
		return $url;
	}
	
	############
	### Time ###
	############
	public int $redirectTime = 0;
	public function redirectTime(int $redirectTime=8) : self
	{
		$this->redirectTime = $redirectTime;
		return $this;
	}
	
	#############
	### Flash ###
	#############
	public string $redirectError;
	public string $redirectMessage;

	public function redirectError(string $key, array $args=null, bool $log=true) : self
	{
		if ($log)
		{
			Logger::logError(ten($key, $args));
		}
		return $this->redirectErrorRaw(t($key, $args), false);
	}
	
	public function redirectErrorRaw(string $message, bool $log=true) : self
	{
		if ($log)
		{
			Logger::logError($message);
		}
		if (class_exists('GDO\\Session\\GDO_Session', false))
		{
			GDO_Session::set('redirect_error', $message);
		}
		$this->redirectError = $message;
		return $this;
	}
	
	public function redirectMessage(string $key, array $args=null, bool $log=true) : self
	{
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		return $this->redirectMessageRaw(t($key, $args), false);
	}
	
	public function redirectMessageRaw(string $message, bool $log=true) : self
	{
		if ($log)
		{
			Logger::logMessage($message);
		}
		$this->redirectMessage = $message;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		return '';
	}
	
	public function renderHTML() : string
	{
		$ajax = '';
		if (Application::instance()->isAjax())
		{
			$ajax = $this->renderAjaxRedirect();
		}
		else
		{
			$url = $this->href;
			$time = $this->redirectTime;
			if ($time > 0)
			{
				hdr("Refresh:$time; url=$url");
			}
			else
			{
				hdr('Location: ' . $url);
			}
		}
		return t('gdt_redirect_to', [html($this->href)]) . $ajax;
	}
	
	private function renderAjaxRedirect()
	{
		return sprintf('<script>setTimeout(function(){ window.location.href="%s" }, %d);</script>',
			$this->href, $this->redirectTime * 1000);
		
	}

}
