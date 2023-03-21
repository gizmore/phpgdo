<?php
namespace GDO\Cronjob;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_String;

/**
 * This table holds info about the cronjob runnings.
 * Scheduled entries are added on each run.
 *
 * @version 6.11.0
 * @since 6.10.4
 * @author gizmore
 */
final class GDO_Cronjob extends GDO
{

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
