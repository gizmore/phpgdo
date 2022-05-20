<?php
namespace GDO\Core;

/**
 * Add JS here.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 * @see Module_Javascript
 */
final class Javascript
{
	###################################
	### Asset loader and obfuscator ###
	###################################
	public static $_JAVASCRIPTS = [];
	public static $_JAVASCRIPT_PRE_INLINE = '';
	public static $_JAVASCRIPT_POST_INLINE = '';
	
	###########
	### Add ###
	###########
	public static function addJS($path)
	{
		self::$_JAVASCRIPTS[] = $path;
	}
	
	public static function addJSPreInline($script_html)
	{
	    self::$_JAVASCRIPT_PRE_INLINE .= $script_html . "\n";
	}
	
	public static function addJSPostInline($script_html)
	{
	    self::$_JAVASCRIPT_POST_INLINE .= $script_html . "\n";
	}
	
	##############
	### Render ###
	##############
	public static function displayJavascripts()
	{
		if (!module_enabled('Javascript'))
		{
			$minfied = false;
		}
		else
		{
			$minfied = Module_Javascript::instance()->cfgMinifyJS();
		}
		
		$back = '';
	    if (Application::instance()->allowJavascript())
	    {
	        $back .= self::displayJavascriptPreInline();
    		$javascripts = $minfied ? MinifyJS::minified(self::$_JAVASCRIPTS) : self::$_JAVASCRIPTS;
    		foreach ($javascripts as $js)
    		{
    			$back .= sprintf('<script src="%s"></script>'."\n", $js);
    		}
    		$back .= self::displayJavascriptPostInline();
	    }
		return $back;
	}
	
	###############
	### Private ###
	###############
	private static function displayJavascriptPreInline()
	{
	    return self::displayJavascriptInline(self::$_JAVASCRIPT_PRE_INLINE);
	}
	
	private static function displayJavascriptPostInline()
	{
	    return self::displayJavascriptInline(self::$_JAVASCRIPT_POST_INLINE);
	}
	
	private static function displayJavascriptInline($inline)
	{
	    return $inline ? sprintf("<script>\n%s\n</script>\n", $inline) : '';
	}
	
}
