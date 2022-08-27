<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;
use GDO\UI\GDT_Error;
use GDO\UI\TextStyle;
use GDO\UI\Color;
use GDO\UI\GDT_Success;

/**
 * General Website utility and storage for header and javascript elements.
 * Keeps lists of assets and feeds them to minifiers.
 * Features redirects and alerts.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.5
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
	 * array<array<string>>
	 * @var array
	 */
	private static array $LINKS = [];
	
	/**
	 * add an html <link>
	 * @param string $type = mime_type
	 * @param mixed $rel relationship (one
	 * @param int $media
	 * @param string $href URL
	 * @see http://www.w3schools.com/tags/tag_link.asp
	 */
	public static function addLink($href, $type, $rel, $title=null)
	{
		self::$LINKS[] = [$href, $type, $rel, $title];
	}
	
	public static function addPrefetch($href, $type)
	{
	    array_unshift(self::$LINKS, [$href, $type, 'prefetch', null]);
	}
	
	/**
	 * Output of {$head_links}
	 * @return string
	 */
	public static function displayLink()
	{
		$back = '';
		
		foreach(self::$LINKS as $link)
		{
			list($href, $type, $rel, $title) = $link;
			$title = $title ? " title=\"$title\"" : '';
			$back .= sprintf('<link rel="%s" type="%s" href="%s"%s />'."\n", $rel, $type, $href, $title);
		}
		
		$back .= CSS::render();
		
		return $back;
	}
	
	############
	### Meta ###
	############
	private static $META = [];
	
	/**
	 * add an html <meta> tag
	 * @param array $meta = array($name, $content, 0=>name;1=>http-equiv);
	 * @param boolean $overwrite overwrite key if exist?
	 * @return boolean false if metakey was not overwritten, otherwise true
	 * @TODO possible without key but same functionality?
	 * @TODO strings as params? addMeta($name, $content, $mode, $overwrite)
	 */
	public static function addMeta(array $metaA, $overwrite=true)
	{
		if ((!$overwrite) && (isset(self::$META[$metaA[0]])) )
		{
			return false;
		}
		self::$META[$metaA[0]] = $metaA;
		return true;
	}
	
	/**
	 * Print head meta tags.
	 * @see addMeta()
	 */
	public static function displayMeta()
	{
		if (Application::$INSTANCE->indexed)
		{
    	    self::$META[] = ['robots', 'index, follow', 'name'];
	    }
	    else
	    {
    	    self::$META[] = ['robots', 'noindex', 'name'];
	    }
	    $back = '';
		foreach (self::$META as $meta)
		{
			list($name, $content, $equiv) = $meta;
            if ($content)
            {
                $back .= sprintf("\t<meta %s=\"%s\" content=\"%s\" />\n",
                	$equiv, $name, $content);
            }
		}
		return $back;
	}

	############
	### HEAD ###
	############
	private static string $HEAD = '';
	
	public static function addHead(string $string) : void
	{
		self::$HEAD .= $string . "\n";
	}
	
	public static function displayHead() : string
	{
		return self::$HEAD;
	}
	
	#############
	### Title ###
	#############
	private static string $TITLE = GDO_SITENAME;
	
	public static function setTitle(string $title) : void
	{
	    self::$TITLE = $title;
	    GDT_Page::instance()->titleRaw(self::displayTitle());
	}
	
	public static function displayTitle() : string
	{
	    $title = html(self::$TITLE);
	    if (module_enabled('Core'))
	    {
    	    if (Module_Core::instance()->cfgSiteShortTitleAppend())
    	    {
    	        $title .= " [" . sitename() . "]";
    	    }
	    }
	    return $title;
	}
	
	#############
	### Error ###
	#############
	public static function message(string $titleRaw, string $key, array $args = null, bool $log = true, int $code = 200)
	{
		$app = Application::$INSTANCE;
		$app->setResponseCode($code);
		if ($app->isCLI() || $app->isUnitTests())
		{
			echo Color::green(t($key, $args)) . "\n";
			flush();
		}
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		$success = GDT_Success::make()->
			titleRaw($titleRaw)->
			text($key, $args);
		$top = GDT_Page::instance()->topResponse();
		$top->addField($success);
	}
	
	public static function error(string $titleRaw, string $key, array $args = null, bool $log = true, int $code = GDO_Error::DEFAULT_ERROR_CODE)
	{
		$app = Application::$INSTANCE;
		$app->setResponseCode($code);
		if ($app->isCLI() || $app->isUnitTests())
		{
			echo TextStyle::bold(Color::red(t($key, $args))) . "\n";
			flush();
		}
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		$error = GDT_Error::make()->
			titleRaw($titleRaw)->
			text($key, $args);
		$top = GDT_Page::instance()->topResponse();
		$top->addField($error);
	}
	
}
