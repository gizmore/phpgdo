<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Text;
use GDO\Core\GDT_Index;

/**
 * User settings for larger blob values, e.g. signature.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
final class GDO_UserSettingBlob extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoCached() : bool { return false; }
	public function gdoColumns() : array
	{
		return [
			GDT_User::make('uset_user')->primary(),
			GDT_Name::make('uset_name')->primary()->unique(false),
			GDT_Text::make('uset_var')->max(65535),
			GDT_ACLRelation::make('uset_relation'),
			GDT_Level::make('uset_level'),
			GDT_Permission::make('uset_permission'),
			GDT_Index::make('uset_user_index')->indexColumns('uset_user,uset_name')->hash(),
		];
	}
	
}
