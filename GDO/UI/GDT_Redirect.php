<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\Application;
use GDO\Session\GDO_Session;
use GDO\Core\Logger;

/**
 * A redirect.
 * Renders <script> in ajax mode.
 * Sets headers in html and can display a message after a redirect (session required).
 * 
 * @TODO rename html to http rendering?
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.11.5
 */
final class GDT_Redirect extends GDT
{
	use WithHREF;
	use WithText;
	
	public static bool $REDIRECTED = false; # Only once
	
	public static function to(string $href) : void
	{
		$top = GDT_Page::instance()->topResponse();
		$top->addField(GDT_Redirect::make()->href($href));
	}
	
	############
	### Back ###
	############
	public function back() : self
	{
		return $this->href(self::hrefBack());
	}
	
	/**
	 * Try to get a referrer URL for hrefBack.
	 */
	public static function hrefBack(string $default=null) : string
	{
		if (Application::$INSTANCE->isCLI())
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
		GDT_Page::instance()->topResponse()->addField(GDT_Error::make()->textRaw($message));

		if ($log)
		{
			Logger::logError($message);
		}
		if (class_exists('GDO\\Session\\GDO_Session', false))
		{
			GDO_Session::set('redirect_error', $message);
		}
		if (Application::$INSTANCE->isCLI())
		{
			echo "$message\n";
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
		GDT_Page::instance()->topResponse()->addField(GDT_Success::make()->textRaw($message));
		
		if ($log)
		{
			Logger::logMessage($message);
		}
		if (class_exists('GDO\\Session\\GDO_Session', false))
		{
			GDO_Session::set('redirect_message', $message);
		}
		if (Application::$INSTANCE->isCLI())
		{
			echo "$message\n";
		}
		$this->redirectMessage = $message;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		if (isset($this->href))
		{
			return t('gdt_redirect_to', [$this->href]);
		}
		return GDT::EMPTY_STRING;
	}
	
	public function renderHTML() : string
	{
		$ajax = '';
		$url = isset($this->href) ? $this->href : $this->hrefBack();
		if (Application::$INSTANCE->isAjax())
		{
			$ajax = $this->renderAjaxRedirect();
		}
		else
		{
			$time = $this->redirectTime;
			if ($time > 0)
			{
				hdr("Refresh: {$time}; url={$url}");
			}
			else
			{
				hdr("Location: {$url}");
			}
		}
		
		$link = GDT_Link::make()->href($url);
		
		return t('gdt_redirect_to', [$link->render()]) . $ajax;
	}
	
	private function renderAjaxRedirect()
	{
		return sprintf('<script>setTimeout(function(){ window.location.href="%s" }, %d);</script>',
			$this->href, $this->redirectTime * 1000);
		
	}

}
