<?php
namespace GDO\Util;

require 'php-filewalker/gizmore/Filewalker.php';

use \gizmore\Filewalker as FW;

/**
 * Wrapper for my own lib :]
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class Filewalker
{
	public static function traverse($path, string $pattern=null, callable $callback_file=null, callable $callback_dir=null, int $recursive=FW::MAX_RECURSION, $args=null, $ds=DIRECTORY_SEPARATOR) : void
	{
		FW::traverse($path, $pattern, $callback_file, $callback_dir, $recursive, $args, $ds);
	}
}
