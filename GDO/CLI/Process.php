<?php
namespace GDO\CLI;

use GDO\Core\Debug;

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
		return PHP_OS === 'WINNT';
	}

	/**
	 * Convert DIR separator for operating System.
	 * On Windows we use backslash.
	 * On Linux we keep forward slash, which is default in GDOv7.
	 * @deprecated Nobody cares?
	 */
	public static function osPath(string $path) : string
	{
		if (self::isWindows())
		{
			return str_replace('/', '\\', $path);
		}
		return $path;
	}

	/**
	 * Get the number of CPU cores.
	 * Used in Module_FFMpeg.
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
	 * Determines if a command exists on the current environment
	 *
	 * @param string $command
	 *        The command to check
	 * @return bool True if the command has been found; otherwise, false.
	 * @author https://stackoverflow.com/a/18540185/13599483
	 */
	public static function commandPath($command, $windowsSuffix = '.*')
	{
		$whereIsCommand = self::isWindows() ? 'where' : 'which';
		$command = self::isWindows() ? "$command$windowsSuffix" : $command;

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
			// $stderr = stream_get_contents($pipes[2]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			proc_close($process);

			# Only return first executeable
			$stdout = str_replace("\r", '', $stdout);
			$files = explode("\n", $stdout);
			$file = trim($files[0]);
			return $file;
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
