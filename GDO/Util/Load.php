<?php
namespace GDO\Util;

use GDO\CLI\Process;

/**
 * Gather System metrics.
 * Call Load::init() to include.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.1
 */
final class Load
{
	# Prefilled with fictive values.
	public static array $STATE = [
		# PERF
		'cpus' => 1,
		'load' => 0.00,
		'clock' => 4.22, # GHz
		# MEM
		'avail' => 1024 * 1024 * 1024,
		'used' => 1024 * 1024 * 512,
		'free' => 1024 * 1024 * 512,
		# HDD
		'hdda' => 256 * 1024 * 1024 * 1024, # avail
		'hddu' => 128 * 1024 * 1024 * 1024, # used
		'hddf' => 128 * 1024 * 1024 * 1024, # free
	];

	public static function init(): void
	{
		self::update();
	}
	
	public static function update(): void
	{
		self::$STATE['cpus'] = self::_getCPUCores();
		self::$STATE['load'] = self::_getServerLoad();
		self::$STATE['clock'] = self::_getServerClock();
		self::$STATE['avail'] = $a = self::_getAvail();
		self::$STATE['used'] = $u = self::_getUsed();
		self::$STATE['free'] = $a - $u;
		self::$STATE['hddf'] = $f = self::_getHDDFree();
		self::$STATE['hdda'] = $a = self::_getHDDAvail();
		self::$STATE['hddu'] = $a - $f;
	}
	
	######################
	### Private Gather ###
	######################
	private static function _getCPUCores(): int
	{
		return (Process::isWindows() ?
			getenv('NUMBER_OF_PROCESSORS') :
			substr_count(file_get_contents('/proc/cpuinfo'), 'processor'));
	}
	
	private static function _getServerLoad(): float
	{
		$load = null;
		
		if (Process::isWindows())
		{
			$cmd = 'wmic cpu get loadpercentage /all';
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
	
	private static function _getServerClock(): float
	{
		return 0.00;
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
		return Process::isWindows() ?
			self::_getAvailWindows() :
			self::_getAvailLinux();
	}
	
	private static function _getUsed(): int
	{
		return Process::isWindows() ?
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
		return Regex::firstMatch($ptrn, $info) * 1024;
	}
	
	private static function _getHDDAvail(): int
	{
		return disk_total_space(GDO_PATH);
	}
	
	private static function _getHDDFree(): int
	{
		return disk_free_space(GDO_PATH);
	}
	
}
