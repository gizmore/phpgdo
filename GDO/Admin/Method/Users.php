<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Table\MethodQueryTable;
use GDO\Core\GDT_String;
use GDO\UI\GDT_IconButton;
use GDO\User\GDT_Username;
use GDO\User\GDO_User;

/**
 * GDO_User table for admins
 * 
 * @author gizmore
 * @see GDO_User
 * @see GDT_Table
 * @version 6.10.6
 * @since 6.0.2
 */
class Users extends MethodQueryTable
{
	use MethodAdmin;
	
	public function getTitleLangKey() { return 'btn_users'; }
	
	public function gdoTable() { return GDO_User::table(); }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function execute() : GDT
	{
		$createLink = GDT_IconButton::make()->icon('create')->href(href('Admin', 'UserCreate'))->label('link_create_user');
		return parent::execute()->addField($createLink);
	}
	
	public function gdoHeaders()
	{
		$gdo = $this->gdoTable();
		return [
			GDT_IconButton::make('edit_admin')->icon('edit'),
			$gdo->gdoColumn('user_id'),
			$gdo->gdoColumn('user_country')->withName(false),
			GDT_String::make('user_name'),
			$gdo->gdoColumn('user_type'),
			$gdo->gdoColumn('user_level'),
			GDT_Username::make('username')->orderable(false),
			$gdo->gdoColumn('user_credits'),
			$gdo->gdoColumn('user_email'),
			$gdo->gdoColumn('user_register_time'),
			$gdo->gdoColumn('user_last_activity'),
		];
	}
	
}
