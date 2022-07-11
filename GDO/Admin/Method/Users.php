<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Table\MethodQueryTable;
use GDO\Core\GDT_String;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;

/**
 * GDO_User table for admins
 * 
 * @author gizmore
 * @see GDO_User
 * @see GDT_Table
 * @version 7.0.0
 * @since 6.0.2
 */
class Users extends MethodQueryTable
{
	use MethodAdmin;
	
	public function getTitleLangKey() { return 'btn_users'; }
	
	public function gdoTable() { return GDO_User::table(); }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function execute()
	{
		$createLink = GDT_Button::make()->icon('create')->href(href('Admin', 'UserCreate'))->label('link_create_user');
		return parent::execute()->addField($createLink);
	}
	
	public function gdoHeaders() : array
	{
		$gdo = $this->gdoTable();
		return [
			GDT_Button::make('edit')->icon('edit')->noLabel(),
			$gdo->gdoColumn('user_id'),
			$gdo->gdoColumn('user_type'),
			GDT_String::make('user_name'),
			GDT_String::make('user_guest_name'),
			$gdo->gdoColumn('user_level'),
			$gdo->gdoColumn('user_last_activity'),
		];
	}
	
}
