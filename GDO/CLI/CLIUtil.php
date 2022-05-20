<?php
namespace GDO\CLI;

/**
 * CLI utility.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class CLIUtil
{
	public static function isCLI() : bool
	{
		return php_sapi_name() === 'cli';
	}
	
	public static function isInteractive() : bool
	{
		return stream_isatty(STDIN);
	}
	
	public static function getSingleCommandLine() : string
	{
		global $argv;
		return implode(' ', $argv);
	}

}
