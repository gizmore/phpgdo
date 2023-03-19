<?php
namespace GDO\Date;

use GDO\Date\Method\RefreshOffsets;
use GDO\Util\Arrays;

/**
 * Install timezone table.
 * Refresh hour offsets.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.0
 */
final class Install
{
	public static function install(Module_Date $module): void
	{
		if (!GDO_Timezone::table()->countWhere())
		{
			self::installB($module);
		}
//		$refresh = RefreshOffsets::make();
//		$refresh->run();
	}

	/**
	 * Bulk insert all timezones.
	 */
	private static function installB(Module_Date $module): void
	{
		$list = timezone_identifiers_list();
		Arrays::remove($list, 'UTC');
		array_unshift($list, 'UTC');
		$dt = new \DateTime();
		$data = [];
		foreach ($list as $tzName)
		{
			$tz = new \DateTimeZone($tzName);
			$of = $tz->getOffset($dt);
			$data[] = [$tzName, $of];
		}
		$table = GDO_Timezone::table();
		$columns = $table->gdoColumnsOnly('tz_name', 'tz_offset');
		GDO_Timezone::bulkInsert($columns, $data);
	}
	
}
