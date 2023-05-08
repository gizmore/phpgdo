<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\Website;
use GDO\Session\GDO_Session;
use GDO\User\GDO_User;

/**
 * A redirect.
 * Renders <script> in ajax mode.
 * Sets headers in html and can display a message after a redirect (session required).
 *
 * @TODO Only allow the first redirect for a request, then error: already redirecting!
 *
 * @version 7.0.3
 * @since 6.11.5
 * @author gizmore
 */
final class GDT_Redirect extends GDT
{

	use WithHREF;
	use WithText;

	final public const CODE = 307;

	final public const DEFAULT_TIMEOUT = 8;

//	@TODO public static bool $REDIRECTED = false; # Only once

	public int $redirectTime = self::DEFAULT_TIMEOUT;

	############
	### Back ###
	############

	/**
	 * Redirect the user.
	 * Works by injecting to top response.
	 * Default redirect target is hrefBack.
	 */
	public static function to(string $href = null, string $key = null, array $args = null, int $time = self::DEFAULT_TIMEOUT): self
	{
		$re = self::make()->href($href ?: self::hrefBack())
						  ->text($key, $args);
		$re->time($key ? $time : 0);
		GDT_Page::instance()->topResponse()
							->addField($re);
		return $re;
	}

	/**
	 * Try to get a referrer URL for hrefBack.
	 */
	public static function hrefBack(string $default = null): string
	{
		if (Application::$INSTANCE->isCLI())
		{
			return $default ?: hrefDefault();
		}

		$url = GDO_User::current()->settingVar('User', 'last_url');
		if (!$url)
		{
			$url = $_SERVER['HTTP_REFERER'] ?? ($default ?: hrefDefault());
		}

		return $url;
	}

	############
	### Time ###
	############

	public function time(int $time = self::DEFAULT_TIMEOUT): self
	{
		$this->redirectTime = $time;
		return $this;
	}

	#############
	### Flash ###
	#############
	public function redirectError(string $key, array $args = null, bool $log = true): self
	{
		Website::error(t('redirect'), $key, $args, $log, self::CODE);
		if (module_enabled('Session'))
		{
			$app = Application::$INSTANCE;
			if ($app->isWebserver())
			{
				GDO_Session::set('redirect_error', t($key, $args));
			}
			$this->time(0);
		}
		return $this;
	}

	public function redirectMessage(string $key, array $args = null, bool $log = true): self
	{
		Website::message(t('redirect'), $key, $args, $log, self::CODE);
		if (module_enabled('Session'))
		{
			$app = Application::$INSTANCE;
			if ($app->isWebserver())
			{
				GDO_Session::set('redirect_message', t($key, $args));
			}
			$this->time(0);
		}
		return $this;
	}

	##############
	### Render ###
	##############
	public function renderCLI(): string
	{
		$text = $this->renderText();
		if (isset($this->href))
		{
			$text .= ' ( ' . html($this->href) . ' )';
		}
		return $text;
	}

	public function renderHTML(): string
	{
		$app = Application::$INSTANCE;

		$url = $this->href ?? self::hrefBack();

		if ($app->isAjax())
		{
			return $this->renderAjaxRedirect();
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

	private function renderAjaxRedirect(): string
	{
		return sprintf("<script>setTimeout(function(){ window.location.href=\"%s\" }, %d)</script>\n",
			$this->href, $this->redirectTime * 1000);
	}

}
