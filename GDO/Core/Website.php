<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\CLI\CLI;
use GDO\UI\GDT_Error;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Success;

/**
 * General Website utility and storage for header and javascript elements.
 * Keeps lists of assets and feeds them to minifiers.
 *
 * @version 7.0.3
 * @since 3.0.5
 * @author gizmore
 * @see Module_Website
 * @see Minifier
 * @see Javascript
 * @see GDO_Session
 * @see GDT_Page
 */
final class Website
{

	############
	### LINK ###
	############
	/**
	 * HTML page LINK elements.
	 */
	private static array $LINKS = [];
	private static array $META = [];
	private static string $HEAD = '';

	############
	### Meta ###
	############
	private static string $TITLE = GDO_SITENAME;

	/**
	 * Add an html <link> to the <head>.
	 * Use href, type, rel, title, sizes, etc... dictionary.
	 *
	 * @see http://www.w3schools.com/tags/tag_link.asp
	 */
	public static function addLink(array $data): void
	{
		self::$LINKS[] = $data;
	}

	/**
	 * Output of {$head_links}
	 */
	public static function displayLink(): string
	{
		$back = '';
		foreach (self::$LINKS as $link)
		{
			$back .= '<link';
			foreach ($link as $k => $v)
			{
				$back .= " {$k}=\"{$v}\"";
			}
			$back .= " />\n";
		}

		$back .= CSS::render();

		return $back;
	}

	############
	### HEAD ###
	############

	/**
	 * add an html <meta> tag
	 */
	public static function addMeta(array $metaA, bool $overwrite = true): bool
	{
		if ((!$overwrite) && (isset(self::$META[$metaA[0]])))
		{
			return false;
		}
		self::$META[$metaA[0]] = $metaA;
		return true;
	}

	/**
	 * Print head meta tags.
	 *
	 * @see addMeta()
	 */
	public static function displayMeta(): string
	{
		/** @var Method $me * */
		global $me;
		if ($me && $me->isIndexed())
		{
			self::$META[] = ['robots', 'index, follow', 'name'];
		}
		else
		{
			self::$META[] = ['robots', 'noindex', 'name'];
		}

		if ($meta = $me->seoMetaImage())
		{
			self::$META[] = $meta;
		}

		$back = '';
		foreach (self::$META as $meta)
		{
			[$name, $content, $equiv] = $meta;
			if ($content)
			{
				$back .= sprintf("\t<meta %s=\"%s\" content=\"%s\" >\n",
					$equiv, $name, $content);
			}
		}
		return $back;
	}

	public static function addHead(string $string): void
	{
		self::$HEAD .= $string . "\n";
	}

	#############
	### Title ###
	#############

	public static function displayHead(): string
	{
		return self::$HEAD;
	}

	public static function setTitle(string $title): void
	{
		self::$TITLE = $title;
		GDT_Page::instance()->titleRaw(self::displayTitle());
	}

	public static function displayTitle(): string
	{
		$title = html(self::$TITLE);
		if (module_enabled('Core'))
		{
			if (Module_Core::instance()->cfgSiteShortTitleAppend())
			{
				$title .= ' [' . sitename() . ']';
			}
		}
		return $title;
	}


	#############
	### Error ###
	#############

	/**
	 * Raw error message.
	 */
	public static function errorRaw(string $titleRaw, string $errorRaw, bool $log = true, int $code = GDO_Exception::GDT_ERROR_CODE): GDT_Response
	{
		return self::error($titleRaw, '%s', [$errorRaw], $log, $code);
	}

	public static function error(string $titleRaw, string $key, array $args = null, bool $log = true, int $code = GDO_Exception::GDT_ERROR_CODE): GDT_Response
	{
		$app = Application::$INSTANCE;
		$app::setResponseCode($code);
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		$error = GDT_Error::make()->
		code($code)->
		titleRaw($titleRaw)->
		text($key, $args);
		$response = GDT_Response::make()->code($code);
		if ($app->isCLIOrUnitTest())
		{
			echo $error->renderCLI();
			if (ob_get_level())
			{
				ob_flush();
			}
		}
		GDT_Page::instance()->topResponse()->addField($error);
		return $response;
	}


	/**
	 * Raw success message.
	 */
	public static function messageRaw(string $titleRaw, string $messageRaw, bool $log = true, int $code = 200): GDT_Response
	{
		return self::message($titleRaw, '%s', [$messageRaw], $log, $code);
	}


	public static function message(string $titleRaw, string $key, array $args = null, bool $log = true, int $code = 200): GDT_Response
	{
		$app = Application::$INSTANCE;
		$app::setResponseCode($code);
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		$success = GDT_Success::make()->
		code($code)->
		titleRaw($titleRaw)->
		text($key, $args);
		$response = GDT_Response::make()->code($code);
		if ($app->isCLIOrUnitTest())
		{
			echo $success->renderCLI();
			if (ob_get_level())
			{
				ob_flush();
			}
		}
		GDT_Page::instance()->topResponse()->addField($success);
		return $response;
	}

}
