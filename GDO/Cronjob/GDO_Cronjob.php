<?php
declare(strict_types=1);
namespace GDO\Cronjob;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_String;
use GDO\Date\Time;

/**
 * This table holds info about the cronjob runnings.
 * Scheduled entries are added on each run.
 *
 * @version 7.0.3
 * @since 6.10.4
 * @author gizmore
 */
final class GDO_Cronjob extends GDO
{

	public static function cleanup(): void
	{
		$cut = Time::getDate(Application::$TIME - Time::ONE_MONTH);
		self::table()->deleteWhere("cron_started < '$cut'");
	}

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('cron_id'),
			GDT_String::make('cron_method')->ascii()->caseS()->notNull(),
			GDT_CreatedAt::make('cron_started'),
			GDT_Checkbox::make('cron_success'),
		];
	}

}
