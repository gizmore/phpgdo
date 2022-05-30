<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;

/**
 * General Website utility and storage for header and javascript elements.
 * Keeps lists of assets and feeds them to minifiers.
 * Features redirects and alerts.
 * 
 * @deprecated It is not nice to have a class Website in core.
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.5
 * @see Module_Website
 * @see Minifier
 * @see Javascript
 * @see GDO_Session
 * @see GDT_Page
 */
final class Website
{
// 	/**
// 	 * Redirection URL
// 	 * @var string
// 	 */
// 	public static ?string $REDIRECTED = null;

	/**
	 * HTML page LINK elements.
	 * array<array<string>>
	 * @var array
	 */
	private static array $LINKS = [];
	
	/**
	 * @param number $time
	 * @return \GDO\Core\GDT_Response
	 */
// 	public static function redirectBack($time=0, $default=null)
// 	{
// 	    return self::redirect(self::hrefBack($default), $time);
// 	}
	
// 	public static function redirect(string $url, int $time=0) : GDT
// 	{
// 		$app = Application::$INSTANCE;
// 	    switch ($app->getFormat())
// 		{
// 			case Application::HTML:
// 				if ($app->isAjax())
// 				{
// 					return GDT_Response::makeWith(GDT_HTML::withHTML(self::ajaxRedirect($url, $time)));
// 				}
// 				elseif (!self::$REDIRECTED)
// 				{
// 					if ($time > 0)
// 					{
// 					    hdr("Refresh:$time; url=$url");
// 					}
// 					else
// 					{
// 						hdr('Location: ' . $url);
// 					}
// 					self::$REDIRECTED = $url;
// 				}
// 		}
// 		GDT_Page::instance()->topResponse()->addField(GDT_Success::with('msg_redirect', [GDT_Link::anchor($url), $time]));
// 	}

// 	private static function ajaxRedirect($url, $time)
// 	{
// 		# Don't do this at home kids!
// 		return sprintf('<script>setTimeout(function(){ window.location.href="%s" }, %d);</script>', $url, $time*1000);
// 	}
	
	public static function addInlineCSS(string $css)
	{
		CSS::addInline($css);
	}
	
	public static function addCSS(string $path)
	{
		CSS::addFile($path);
	}
	
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
	
	/**
	 * Renders a json response and dies.
	 * 
	 * @param mixed $json
	 * @param boolean $die
	 */
// 	public static function renderJSON($json) : string
// 	{
// 	    if (!Application::$INSTANCE->isCLI())
// 		{
// 			hdr('Content-Type: application/json');
// 		}
// 		return json_encode($json, GDO_JSON_DEBUG?JSON_PRETTY_PRINT:0); # pretty json
// 	}
	
// 	public static function outputStarted() : bool
// 	{
// 		return headers_sent() || ob_get_contents();
// 	}
	
	#############
	### Error ###
	#############
// 	public static function error($key, array $args=null, $code=409)
// 	{
// 	    self::topResponse()->addField(GDT_Error::with($key, $args, $code));
// 	}
	
// 	/**
// 	 * Redirect and show a message at the new page.
// 	 * @param string $key
// 	 * @param array $args
// 	 * @param string $url
// 	 * @param number $time
// 	 * @return \GDO\Core\GDT_Response
// 	 */
// 	public static function redirectMessage($key, array $args=null, $url=null, $time=0)
// 	{
// 	    return self::redirectMessageRaw(t($key, $args), $url, $time);
// 	}
	
// 	public static function redirectMessageRaw(string $message, string $url=null, int $time=0) : GDT
// 	{
// 	    $app = Application::$INSTANCE;
	 
// 	    GDT_Page::instance()->topResponse()->addField(GDT_Success::make()->textRaw($message));
	  
// // 	    if ($app->isCLI() || $app->isUnitTests())
// // 	    {
// // 	        if ($app->isUnitTests())
// // 	        {
// // 	            echo "Redirect => $url\n";
// // 	        }
// // 	        echo "{$message}\n";
// // 	        return;
// // 	    }
	    
// 	    $url = $url === null ? self::hrefBack() : $url;
	    
// // 	    if (!$app->isInstall())
// // 	    {
// 	        GDO_Session::set('redirect_message', $message);
// 	        return self::redirect($url, $time);
// // 	    }
// 	}
	
// 	public static function redirectError($key, array $args=null, $url=null, $time=0, $code=409)
// 	{
// 		return self::redirectErrorRaw(t($key, $args), $url, $time, $code);
// 	}
	
// 	public static function redirectErrorRaw($message, $url=null, $time=0, $code=409)
// 	{
// 	    $app = Application::$INSTANCE;

// 	    self::topResponse()->addField(GDT_Error::make()->textRaw($message, $code));
	    
// 	    if ($app->isCLI())
// 	    {
// 	        echo "{$message}\n";
// 	        return;
// 	    }
	    
// 	    $url = $url === null ? self::hrefBack() : $url;
// 	    if (!$app->isInstall())
// 	    {
// 	        GDO_Session::set('redirect_error', $message);
// 	        return self::redirect($url, $time);
// 	    }
// 	}
	
	####################
	### Top Response ###
	####################
// 	public static $TOP_RESPONSE = null;
// 	public static function topResponse()
// 	{
// 	    if (self::$TOP_RESPONSE === null)
// 	    {
// 	        self::$TOP_RESPONSE = GDT_Container::make('topResponse');
// 	        if (!Application::$INSTANCE->isInstall())
// 	        {
// 	            if ($message = GDO_Session::get('redirect_message'))
// 	            {
// 	                GDO_Session::remove('redirect_message');
// 	                self::$TOP_RESPONSE->addField(GDT_Success::make()->textRaw($message));
// 	            }
// 	            if ($message = GDO_Session::get('redirect_error'))
// 	            {
// 	                GDO_Session::remove('redirect_error');
// 	                self::$TOP_RESPONSE->addField(GDT_Error::make()->textRaw($message));
// 	            }
// 	        }
// 	    }
// 	    return self::$TOP_RESPONSE;
// 	}
	
// 	public static function renderTopResponse()
// 	{
// 	    return self::topResponse()->render();
// 	}
	
// 	#####################
// 	### JSON Response ###
// 	#####################
// 	public static $JSON_RESPONSE;
// 	public static function jsonResponse()
// 	{
// 	    if (!self::$JSON_RESPONSE)
// 	    {
// 	        self::$JSON_RESPONSE = GDT_Response::make();
// 	    }
// 	    return self::$JSON_RESPONSE;
// 	}
	
// 	public static function renderJSONResponse()
// 	{
// 	    if (self::$JSON_RESPONSE)
// 	    {
// 	        return self::$JSON_RESPONSE->renderJSON();
// 	    }
// 	}
	
	####################
	### Generic Head ###
	####################
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

}
