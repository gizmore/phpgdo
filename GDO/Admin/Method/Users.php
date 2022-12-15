<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Table\MethodQueryTable;
use GDO\Core\GDO;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\UI\GDT_EditButton;

/**
 * GDO_User table for staff.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.2
 * @see GDO_User
 * @see GDT_Table
 */
class Users extends MethodQueryTable
{
	use MethodAdmin;
	
	public function getMethodTitle() : string
	{
		return t('btn_users');
	}
	
	public function gdoTable() : GDO
	{
		return GDO_User::table();
	}
	
	public function execute()
	{
		$createLink = GDT_Button::make()->icon('create')->href(href('Admin', 'UserCreate'))->label('link_create_user');
		return parent::execute()->addField($createLink);
	}
	
	public function gdoHeaders() : array
	{
		$gdo = $this->gdoTable();
		return [
			GDT_EditButton::make()->labelNone(),
			$gdo->gdoColumn('user_id'),
			$gdo->gdoColumn('user_type'),
			$gdo->gdoColumn('user_name'),
// 			$gdo->gdoColumn('user_guest_name'),
			$gdo->gdoColumn('user_level'),
// 			$gdo->gdoColumn('user_last_activity'),
			$gdo->gdoColumn('user_deleted'),
			$gdo->gdoColumn('user_deletor'),
		];
	}
	
}
