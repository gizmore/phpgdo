<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Table\GDT_Count;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_DeleteButton;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\User\GDT_Permission;
use GDO\User\GDO_Permission;
use GDO\Core\GDO;
use GDO\UI\GDT_Button;

/**
 * View all users with a permission.
 * 
 * @version 7.0.0
 * @since 3.0.2
 * @author gizmore
 */
class ViewPermission extends MethodQueryTable
{
	use MethodAdmin;
	
	private GDO_Permission $permission;
	
	public function getPermission() : ?string { return 'staff'; }

	public function gdoParameters() : array
	{
	    return [
	        GDT_Permission::make('permission')->notNull(),
	    ];
	}
	
	public function gdoTable() : GDO
	{
	    return GDO_UserPermission::table();
	}
	
	public function gdoFetchAs()
	{
		return GDO_User::table();
	}
	
	public function getConfigPermission() : GDO_Permission
	{
		if (!isset($this->permission))
		{
			$this->permission = $this->gdoParameterValue('permission');
		}
		return $this->permission;
	}
	
	public function gdoHeaders() : array
	{
		return [
			GDT_Count::make('count'),
			GDT_User::make('perm_user_id'),
			GDT_CreatedAt::make('perm_created_at'),
			GDT_CreatedBy::make('perm_created_by'),
			GDT_Button::make('perm_add')->icon('add'),
			GDT_DeleteButton::make('perm_revoke'),
		];
	}
	
	protected function gdoTableHREF() : string
	{
		return $this->href('&permission='.$this->getConfigPermission()->getID());
	}
	
	public function getQuery()
	{
		return $this->gdoTable()->
			select('*,perm_user_id_t.*, perm_perm_id_t.*')->
			joinObject('perm_user_id')->
			joinObject('perm_perm_id')->
			where('perm_perm_id='.$this->getConfigPermission()->getID())->
			uncached();
	}
	
}
