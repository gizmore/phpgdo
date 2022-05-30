<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Table\GDT_Count;
use GDO\Table\GDT_Table;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_DeleteButton;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\User\GDT_Permission;

/**
 * View all users with a permission.
 * @version 7.0.0
 * @since 3.0.2
 * @author gizmore
 */
class ViewPermission extends MethodQueryTable
{
	use MethodAdmin;
	
	private $permission;
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function gdoTable()
	{
	    return GDO_UserPermission::table();
	}
	
	public function createTable(GDT_Table $table)
	{
	    $table->fetchAs(GDO_User::table());
	}
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_Permission::make('permission')->notNull(),
	    ];
	}
	
	public function onInit() : void
	{
// 		parent::onInit();
		$this->permission = $this->gdoParameterValue('permission');
	}
	
	public function gdoHeaders()
	{
		return [
			GDT_Count::make('count'),
			GDT_User::make('perm_user_id'),
			GDT_CreatedAt::make('perm_created_at'),
			GDT_CreatedBy::make('perm_created_by'),
			GDT_DeleteButton::make('perm_revoke'),
		];
	}
	
	public function getQuery()
	{
		return $this->gdoTable()->
			select('perm_user_id_t.*, perm_perm_id_t.*')->
			joinObject('perm_user_id')->
			joinObject('perm_perm_id')->
			where('perm_perm_id='.$this->permission->getID())->
			uncached();
	}
    
// 	public function execute()
// 	{
// 	    $this->renderPermTabs('Admin');
// 	    return parent::execute();
// 	}
	
}
