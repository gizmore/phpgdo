<?php
namespace GDO\User\Method;

use GDO\Core\GDO;
use GDO\User\GDO_User;
use GDO\Core\GDT_JSON;
use GDO\Core\MethodCompletion;

/**
 * Auto completion for GDT_User.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class Completion extends MethodCompletion
{
	public function getMethodTitle() : string
	{
		return "Autocompletion for users";
	}
	
	public function getMethodDescription() : string
	{
		return "Autocompletion for users";
	}
	
	public function execute()
	{
		$q = $this->getSearchTerm();
		$q = GDO::escapeSearchS($q);
		$condition = sprintf('user_type IN ("guest","member") AND user_name LIKE \'%%%1$s%%\' OR user_guest_name LIKE \'%%%1$s%%\'', $q);
		$query = GDO_User::table()->select()->where($condition)->limit($this->getMaxSuggestions())->uncached();
		$result = $query->exec();
		$response = [];
		
		/** @var $user GDO_User **/
		while ($user = $result->fetchObject())
		{
			$response[] = array(
				'id' => $user->getID(),
				'json' => array(
					'user_name' => $user->getName(),
					'user_language' => $user->getLangISO(),
				),
				'text' => $user->renderUserName(),
			    'display' => $user->renderChoice(),
			);
		}
	
		return GDT_JSON::make()->value($response);
	}
}
