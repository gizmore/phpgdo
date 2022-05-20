<?php
namespace GDO\Core;

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
		throw new GDO_StubException();
	}

}
