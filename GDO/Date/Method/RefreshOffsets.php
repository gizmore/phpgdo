<?php
namespace GDO\Date\Method;

use DateTime;
use GDO\Cronjob\MethodCronjob;
use GDO\Date\GDO_Timezone;

/**
 * Refresh timezone offsets which might change on summer/wintertime or wars.
 *
 * @version 6.11.0
 * @since 6.11.0
 * @author gizmore
 */
final class RefreshOffsets extends MethodCronjob
{

	public function isTrivial(): bool { return false; }

	public function runAt(): string
	{
		return $this->runDailyAt(2);
	}

	public function run(): void
	{
		$result = GDO_Timezone::table()->select()->exec();
		while ($tz = $result->fetchObject())
		{
			$this->update($tz);
		}
	}

	public function update(GDO_Timezone $tz)
	{
		$datetime = new DateTime();
		$timezone = $tz->getTimezone();
		$offset = $timezone->getOffset($datetime);
		$tz->saveVar('tz_offset', $offset / 60, false);
	}

}
