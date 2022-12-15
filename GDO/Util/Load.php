<?php
namespace GDO\Util;

/**
 * Gather System metrics.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class Load
{
	public static array $STATE = [
		'cpus' => 1,
		'load' => 0.00,
		'avail' => 1024 * 1024 * 1024,
		'used' => 1024 * 1024 * 512,
		'free' => 1024 * 1024 * 512,
	];
	
	public static function update(): void
	{
		self::$STATE['cpus'] = self::_getCPUCores();
		self::$STATE['load'] = self::_getServerLoad();
		self::$STATE['avail'] = $a = self::_getAvail();
		self::$STATE['used'] = $u = self::_getUsed();
		self::$STATE['free'] = $a - $u;
	}
	
	public static function getLoadAvg(): float
	{
		return self::$STATE['load'];
	}
	
	public static function getLoadMax(): float
	{
		return self::$STATE['cpus'];
	}
	
	public static function getMemFree(): int
	{
		return self::$STATE['free'];
	}

	public static function getMemAvail(): int
	{
		return self::$STATE['avail'];
	}
	
	######################
	### Private Gather ###
	######################
	private static function _getCPUCores(): int
	{
		return ((PHP_OS_FAMILY == 'Windows') ?
			getenv('NUMBER_OF_PROCESSORS') :
			substr_count(file_get_contents('/proc/cpuinfo'), 'processor'));
	}
	
	private static function _getServerLoad(): float
	{
		$load = null;
		
		if (stristr(PHP_OS, "win"))
		{
			$cmd = "wmic cpu get loadpercentage /all";
			$load = self::_windowsStats($cmd);
			$load /= 100.0;
		}
		else
		{
			$output = file_get_contents('/proc/loadavg');
			$load = $output ?
				substr($output, 0, strpos($output, ' ')) :
				9.99;
		}
		
		return $load;
	}
	
	private static function _windowsStats(string $cmd): ?string
	{
		$output = [];
		exec($cmd, $output);
		if ($output)
		{
			foreach ($output as $line)
			{
				if ($line && preg_match('/^[0-9]+$/', $line))
				{
					return $line;
				}
			}
		}
		return null;
	}

	private static function _getAvail(): int
	{
		return stristr(PHP_OS, "win") ?
			self::_getAvailWindows() :
			self::_getAvailLinux();
	}
	
	private static function _getUsed(): int
	{
		return stristr(PHP_OS, "win") ?
			self::_getUsedWindows() :
			self::_getUsedLinux();
	}
	
	private static function _getAvailWindows(): int
	{
		$cmd = 'wmic memorychip get capacity';
		return self::_windowsStats($cmd);
	}
	
	private static function _getUsedWindows(): int
	{
		$cmd = 'wmic OS get FreePhysicalMemory';
		$free = self::_windowsStats($cmd);
		$free *= 1024;
		$total = self::_getAvailWindows();
		return $total - $free;
	}
	
	private static function _getAvailLinux(): int
	{
		return self::_getMemLinux('MemTotal');
	}
	
	private static function _getUsedLinux(): int
	{
		return self::_getMemLinux('MemFree');
	}
	
	private static function _getMemLinux(string $key): int
	{
		$ptrn = '/'.$key.':\s*([0-9]+) kB/';
		$info = file_get_contents('/proc/meminfo');
		return Regex::firstMatch($ptrn, $info);
	}
	
}
