<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Index;

/**
 * Similiar to modulevars, this table is for user vars.
 * @see GDO_Module for user settings API.
 * 
 * @hook UserSettingChange(GDO_User, key, var)
 * 
 * @author gizmore@wechall.net
 * @version 6.10
 * @since 6.00
 */
final class GDO_UserSetting extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoCached() : bool { return false; }
	public function gdoDependencies() { return ['GDO\User\GDO_User', 'GDO\Core\GDO_Module']; }
	public function gdoColumns() : array
	{
		return array(
			GDT_User::make('uset_user')->primary(),
			GDT_Name::make('uset_name')->primary()->unique(false),
			GDT_String::make('uset_value'),
		    GDT_Index::make('uset_user_index')->indexColumns('uset_user')->hash(),
		);
	}
	
}
