<?php
namespace GDO\Core;

use GDO\Javascript\MinifyJS;

/**
 * Add JS here.
 * Can make use of minifier.
 *
 * @version 7.0.1
 * @since 6.0.0
 * @author gizmore
 * @see Module_Javascript
 */
final class Javascript
{

	###################################
	### Asset loader and obfuscator ###
	###################################
	public static array $_JAVASCRIPTS = [];
	public static string $_JAVASCRIPT_PRE_INLINE = '';
	public static string $_JAVASCRIPT_POST_INLINE = '';

	###########
	### Add ###
	###########
	public static function addJS(string $path): void
	{
		self::$_JAVASCRIPTS[] = $path;
	}

	public static function addJSPreInline(string $script_html): void
	{
		self::$_JAVASCRIPT_PRE_INLINE .= $script_html . "\n";
	}

	public static function addJSPostInline(string $script_html): void
	{
		self::$_JAVASCRIPT_POST_INLINE .= $script_html . "\n";
	}

	##############
	### Render ###
	##############
	public static function displayJavascripts(): string
	{
		$minify = GDO_Module::config_var('Javascript', 'minify_js', 'no');
		$minify = $minify === 'concat';

		$back = '';
		if (GDO_Module::config_var('Core', 'allow_javascript', '1'))
		{
			$back .= self::displayJavascriptPreInline();
			$javascripts = $minify ? MinifyJS::minified(self::$_JAVASCRIPTS) : self::$_JAVASCRIPTS;
			foreach ($javascripts as $js)
			{
				$back .= sprintf('<script src="%s"></script>' . "\n", $js);
			}
			$back .= self::displayJavascriptPostInline();
		}
		return $back;
	}

	###############
	### Private ###
	###############
	private static function displayJavascriptPreInline(): string
	{
		return self::displayJavascriptInline(self::$_JAVASCRIPT_PRE_INLINE);
	}

	private static function displayJavascriptInline(string $inline): string
	{
		return $inline ? sprintf("<script>\n%s\n</script>\n", $inline) : '';
	}

	private static function displayJavascriptPostInline(): string
	{
		return self::displayJavascriptInline(self::$_JAVASCRIPT_POST_INLINE);
	}

}
