<?php
namespace GDO\Core;

use GDO\CSS\Minifier;

/**
 * CSS asset storage.
 * Can be given to CSSMinify for asset minification.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
final class CSS
{
	public static array $FILES = [];
	public static string $INLINE = '';
	
	public static function addFile(string $path) : void
	{
		self::$FILES[] = $path;
	}
	
	public static function addInline(string $css) : void
	{
		self::$INLINE .= $css . "\n";
	}
	
	public static function render()
	{
		# Let module CSS handle it
		if (GDO_Module::config_var('CSS', 'minify_css', '0'))
		{
			return Minifier::renderMinified();
		}
		
		# Render original basics
		$back = '';
		foreach (self::$FILES as $path)
		{
			$back .= sprintf("\t<link rel=\"stylesheet\" href=\"%s\" /> \n", $path);
		}
		if (self::$INLINE)
		{
			$back .= sprintf("\t<style><!--\n\t%s\n\t--></style>\n", self::$INLINE);
		}
		return $back;
	}

}
