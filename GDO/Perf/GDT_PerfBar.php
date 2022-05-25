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

/**
 * Performance statistics panel.
 * 
 * @author gizmore
 * @version 7.0.0
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
		return array(
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
			'memory_max' => max($memphp, $memreal), # Bug in PHP?
			
			'phpClasses' => count(get_declared_classes()),
			
			'gdoFiles' => $GDT_LOADED,
		    'gdoCount' => GDO::$GDO_COUNT,
			'gdtCount' => GDT::$GDT_COUNT,
			'gdtPeakCount' => GDT::$GDT_PEAKS,
			'funcCount' => xdebug_get_function_count(),
		    'gdoModules' => count(ModuleLoader::instance()->getEnabledModules()),
			'gdoLangFiles' => Trans::numFiles(),
			'gdoTemplates' => GDT_Template::$CALLS,
			'gdoHooks' => GDT_Hook::$CALLS,
// 		    'gdoHookNames' => GDT_Hook::$CALL_NAMES,
			'gdoIPC' => GDT_Hook::$IPC_CALLS,
			'gdoMails' => Mail::$SENT,
		);
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
		return Application::runtime();
	}
	
}
