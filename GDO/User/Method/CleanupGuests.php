<?php
declare(strict_types=1);
namespace GDO\User\Method;

use GDO\Core\Application;
use GDO\Cronjob\MethodCronjob;
use GDO\Date\Time;
use GDO\User\GDO_UserSetting;

/**
 * Cleanup old guest accounts that are unused.
 *
 * @version 7.0.3
 * @since 6.10.3
 * @author gizmore
 */
final class CleanupGuests extends MethodCronjob
{

	public function runAt(): string
	{
		return $this->runDailyAt(1);
	}

	public function run(): void
	{
		# Basically fetch all users with no recent activity.
		$cut = Time::getDate(Application::$TIME - GDO_SESS_TIME);
		$query = GDO_UserSetting::usersWithQuery('User', 'last_activity', $cut, '<');
		# And we want only guests
		$query->where('user_type="guest"');

		$result = $query->exec();
		$numDeleted = 0;
		while ($user = $result->fetchObject())
		{
			$user->delete();
			$numDeleted++;
		}
		if ($numDeleted > 0)
		{
			$this->logNotice(sprintf('Deleted %d guest users', $numDeleted));
		}
	}

}
