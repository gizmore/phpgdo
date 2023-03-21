<?php
namespace GDO\User\Method;

use GDO\Core\GDO;
use GDO\Core\GDT_JSON;
use GDO\Core\MethodCompletion;
use GDO\User\GDO_User;

/**
 * Auto completion for GDT_User.
 *
 * @version 7.0.1
 * @since 5.0.0
 * @author gizmore
 */
class Completion extends MethodCompletion
{

	protected function gdoTable(): GDO
	{
		return GDO_User::table();
	}

	public function getMethodTitle(): string
	{
		return 'User Autocompletion';
	}

	public function getMethodDescription(): string
	{
		return 'Autocompletion for users on ' . sitename();
	}

	public function execute()
	{
		$q = $this->getSearchTerm();
		$q = GDO::escapeSearchS($q);
		$condition = sprintf('user_type IN ("guest","member") AND user_name LIKE \'%%%1$s%%\' OR user_guest_name LIKE \'%%%1$s%%\'', $q);
		$query = GDO_User::table()->select()->where($condition)->limit($this->getMaxSuggestions())->uncached();
		$result = $query->exec();
		$response = [];

		/** @var $user GDO_User * */
		while ($user = $result->fetchObject())
		{
			$response[] = [
				'id' => $user->getID(),
				'json' => [
					'user_name' => $user->getName(),
					'user_language' => $user->getLangISO(),
				],
				'text' => $user->renderUserName(),
				'display' => $user->renderOption(),
			];
		}

		return GDT_JSON::make()->value($response);
	}

}
