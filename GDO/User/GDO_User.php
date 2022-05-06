<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;

/**
 * The holy user class.
 * Has temp data.
 * Most user related fields are in other module settings.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 1.0.0
 */
final class GDO_User extends GDO
{
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('user_id'),
			GDT_String::make('user_name'),
			
		];
	}
	
}
