<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\Application;
use GDO\Session\GDO_Session;
use GDO\Core\Website;

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
	
	const CODE = 307;
	
	public static bool $REDIRECTED = false; # Only once
	
	/**
	 * @param string $href
	 */
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
	public function redirectError(string $key, array $args=null, bool $log=true) : self
	{
		Website::error(t('redirect'), $key, $args, $log, self::CODE);
// 		$error = GDT_Error::make()->code(self::CODE)->text($key, $args);
// 		GDT_Page::instance()->topResponse()->addField($error);
		if (module_enabled('Session'))
		{
			$app = Application::$INSTANCE;
			if ($app->isWebserver())
			{
				GDO_Session::set('redirect_error', t($key, $args));
			}
		}
		return $this;
	}
	
	public function redirectMessage(string $key, array $args=null, bool $log=true) : self
	{
		Website::message(t('redirect'), $key, $args, $log, self::CODE);
// 		$success = GDT_Success::make()->code(self::CODE)->text($key, $args);
// 		GDT_Page::instance()->topResponse()->addField($success);
		if (module_enabled('Session'))
		{
			$app = Application::$INSTANCE;
			if ($app->isWebserver())
			{
				GDO_Session::set('redirect_message', t($key, $args));
			}
		}
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		return GDT::EMPTY_STRING;
	}
	
	public function renderHTML() : string
	{
		$app = Application::$INSTANCE;
		
// 		$ajax = '';
		$url = isset($this->href) ? $this->href : $this->hrefBack();
		if ($app->isAjax())
		{
// 			$ajax = $this->renderAjaxRedirect();
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
		$link = $link->render();
		return GDT_Panel::make()->text('gdt_redirect_to', [$link])->render();
	}
	
	private function renderAjaxRedirect()
	{
		return sprintf('<script>setTimeout(function(){ window.location.href="%s" }, %d);</script>',
			$this->href, $this->redirectTime * 1000);
	}

}
