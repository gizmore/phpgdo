<?php
namespace GDO\CLI;

use GDO\Core\Debug;
use GDO\Core\Website;

/**
 * Process utilities.
 * Check if Windows is used.
 * Check if a command is in PATH env.
 * Turn pathes to OS DIR_SEPARATOR path.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.0
 */
final class Process
{

	/**
	 * Check if the operating system is Windows.
	 */
	public static function isWindows() : bool
	{
		return stristr(PHP_OS, 'win');
	}

	/**
	 * Convert DIR separator for operating System.
	 * On Windows we use backslash.
	 * On Linux we keep forward slash, which is default in GDOv7.
	 */
	public static function osPath(string $path) : string
	{
		return str_replace('/', '\\', $path); #PP#windows#
		return $path; #PP#linux#
	}

	/**
	 * Get the number of CPU cores.
	 * Used in Module_FFMpeg.
	 * @deprecated Use \GDO\Util\Load::$STATS['cores'] instead.
	 */
	public static function cores() : int
	{
		try
		{
			if (self::isWindows())
			{
				return (int) getenv('NUMBER_OF_PROCESSORS');
			}
			else
			{
				return substr_count(file_get_contents('/proc/cpuinfo'), 'processor');
			}
		}
		catch (\Throwable $ex)
		{
			Debug::debugException($ex, false);
			return 1;
		}
	}
	
	/**
	 * Determines if a command exists on the current environment.
	 * On success it optionally shows a success message. This method is only used in detection. You can also set a path manually in those settings, if your PATH does not contain a binary.
	 *
	 * @param string $command
	 *        The command to check
	 * @return bool True if the command has been found; otherwise, false.
	 * @author https://stackoverflow.com/a/18540185/13599483
	 */
	public static function commandPath(string $command, string $windowsSuffix = '.*', bool $alert=true) : ?string
	{
		$whereIsCommand = self::isWindows() ? 'where /R %userprofile% ' : 'which';
		$command = self::isWindows() ? $command . $windowsSuffix : $command;

		$pipes = [];
		$process = proc_open("$whereIsCommand $command",
			[
				0 => ['pipe', 'r'],
				1 => ['pipe', 'w'],
				2 => ['pipe', 'w'],
			], $pipes);

		if ($process !== false)
		{
			$stdout = stream_get_contents($pipes[1]);
// 			$stderr = stream_get_contents($pipes[2]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			proc_close($process);

			# Only return first executeable
			$stdout = str_replace("\r", '', $stdout);
			$files = explode("\n", $stdout);
			if ($file = trim($files[0]))
			{
				if ($alert)
				{
					Website::message(t('module_cli'), 'msg_binary_detected', [$command]);
				}
				return $file;
			}
		}
		return null;
	}

	/**
	 * Execute a shell command.
	 */
	public static function exec(string $cmd): ?array
	{
		$output = null;
		$result = exec($cmd, $output);
		return $result === 0 ? $output : null;
	}

}
