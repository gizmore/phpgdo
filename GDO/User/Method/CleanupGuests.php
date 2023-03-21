<?php
namespace GDO\User\Method;

use GDO\Core\Application;
use GDO\Cronjob\MethodCronjob;
use GDO\Date\Time;
use GDO\DB\Database;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;

/**
 * Cleanup old guest accounts that are unused.
 *
 * @version 7.0.1
 * @since 6.10.3
 * @author gizmore
 */
final class CleanupGuests extends MethodCronjob
{

	public function runEvery()
	{
		return Time::ONE_DAY;
	}

	public function run()
	{
		# Basically fetch all users with no recent activity.
		$cut = Time::getDate(Application::$TIME - GDO_SESS_TIME);
		$query = GDO_UserSetting::usersWithQuery('User', 'last_activity', $cut, '<');
		# And we want only guests
		$query->where('user_type="guest"');
		# And we turn this into a delete
		$query->delete(GDO_User::table()->gdoTableName());
		# Exec
		$query->exec();
		# Stats
		$numDeleted = Database::instance()->affectedRows();
		if ($numDeleted > 0)
		{
			$this->logNotice(sprintf('Deleted %d guest users', $numDeleted));
		}
	}

}
