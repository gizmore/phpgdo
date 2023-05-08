<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDT_Index;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Text;

/**
 * User settings for larger blob values, e.g. signature.
 * This entity is exactly the same as a user setting, except the var field is bigger.
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 */
final class GDO_UserSettingBlob extends GDO_UserSetting
{

	###########
	### GDO ###
	###########
	public function gdoColumns(): array
	{
		return [
			GDT_User::make('uset_user')->primary(),
			GDT_Name::make('uset_name')->caseS()->primary()->unique(false),
			GDT_Text::make('uset_var')->max(65535),
			GDT_ACLRelation::make('uset_relation'),
			GDT_Level::make('uset_level'),
			GDT_Permission::make('uset_permission'),
			GDT_Index::make('uset_user_index')->indexColumns('uset_user')->hash(),
			GDT_Index::make('uset_name_index')->indexColumns('uset_name')->hash(),
		];
	}

}
