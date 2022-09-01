<?php
namespace GDO\Perf;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Core\GDT_Template;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_Hook;
use GDO\UI\GDT_Panel;
use GDO\Mail\Mail;
use GDO\CLI\CLI;
use GDO\Core\GDT_UInt;
use GDO\DB\Cache;
use GDO\CLI\Process;

/**
 * Performance statistics panel.
 * New: Added info from @see \getrusage()
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.1
 */
final class GDT_PerfBar extends GDT_Panel
{
	/**
	 * Gather performance statistics.
	 * 
	 * @return int[string]
	 */
	public static function data() : array
	{
		global $GDT_LOADED;
		$totalTime = xdebug_time_index();
		$phpTime = $totalTime - Database::$QUERY_TIME;
		$memphp = memory_get_peak_usage(false);
		$memreal = memory_get_peak_usage(true);
		
		$res = self::getResourceUsage();
		
		return [
			'logWrites' => Logger::$WRITES,

			'dbReads' => Database::$READS,
			'dbWrites' => Database::$WRITES,
			'dbCommits' => Database::$COMMITS,
			'dbQueries' => Database::$QUERIES,
			'dbQPS' => Database::$QUERIES / $totalTime,

			'dbTime' => round(Database::$QUERY_TIME, 4),
			'phpTime' => round($phpTime, 4),
			'totalTime' => round($totalTime, 4),
			
			'memory_php' => $memphp,
			'memory_real' => $memreal,
			'memory_max' => max([$memphp, $memreal]), # Bug in PHP?
			
			'phpClasses' => count(get_declared_classes()),
			'allocs' => (spl_object_id(GDT_UInt::make())),
			
			'fileCacheHits' => Cache::$CACHE_HITS,
			'fileCacheMiss' => Cache::$CACHE_MISSES,
			'fileCacheRq' => Cache::$CACHE_HITS + Cache::$CACHE_MISSES,
			
			'gdoFiles' => $GDT_LOADED,
			'gdoCount' => GDO::$GDO_COUNT,
			'gdoPeakCount' => GDO::$GDO_PEAKS,
			'gdtCount' => GDT::$GDT_COUNT,
			'gdtPeakCount' => GDT::$GDT_PEAKS,
			'funcCount' => xdebug_get_function_count(),
		    'gdoModules' => count(ModuleLoader::instance()->getEnabledModules()),
			'gdoLangFiles' => Trans::numFiles(),
			'gdoTemplates' => GDT_Template::$CALLS,
			'gdoHooks' => GDT_Hook::$CALLS,
// 		    'gdoHookNames' => GDT_Hook::$CALL_NAMES,
			'gdoIPC' => GDT_Hook::$IPC_CALLS,
			'gdoMails' => self::getMailCount(),
			
			'blocksSent' => $res['ru_oublock'],
			'blocksReceived' => $res['ru_inblock'],
			
			'ipcSent' => $res['ru_msgsnd'],
			'ipcReceived' => $res['ru_msgrcv'],
			
			'rssMaximum' => $res['ru_maxrss'],
			'rssShared' => $res['ru_ixrss'],
			'rssUnshared' => $res['ru_idrss'],
		
			'pageSoft' => $res['ru_minflt'],
			'pageHard' => $res['ru_majflt'],
			
			'signals' => $res['ru_nsignals'],
			
			'ctxSwitchV' => $res['ru_nvcsw'],
			'ctxSwitchIV' => $res['ru_nivcsw'],
			'ctxSwap' => $res['ru_nswap'],
		];
	}
	
	public static function getResourceUsage() : array
	{
		$res = getrusage();
		if (Process::isWindows())
		{
			$res = array_merge($res, [
				'ru_oublock' => '?',
				'ru_inblock' => '?',
				
				'ru_msgsnd' => '?',
				'ru_msgrcv' => '?',
				
// 				'ru_maxrss' => $res['ru_maxrss'],
				'ru_ixrss' => '?',
				'ru_idrss' => '?',
				
				'ru_minflt' => '?',
// 				'ru_majflt' => $res['ru_majflt'],
				
				'ru_nsignals' => '?',
				
				'ru_nvcsw' => '?',
				'ru_nivcsw' => '?',
				'ru_nswap' => '?',
			]);
		}
		else
		{
			$res = array_merge($res, [
				'ru_nswap' => '?',
			]);
		}
		return $res;
	}
	
	private static function getMailCount() : int
	{
		return module_enabled('Mail') ?
			Mail::$SENT : 0;
	}

	public function renderCLI() : string
	{
		return CLI::displayCLI($this->renderHTML());
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('Perf', 'perfbar_html.php', ['bar' => $this]);
	}

}

# Shim
if (!function_exists('xdebug_get_function_count'))
{
	function xdebug_get_function_count() : int
	{
		return 0;
	}
	
	function xdebug_time_index()
	{
		return Application::getRuntime();
	}
	
}
