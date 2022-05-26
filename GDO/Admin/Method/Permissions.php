<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Table\MethodQueryTable;
use GDO\User\GDO_Permission;
use GDO\UI\GDT_EditButton;

/**
 * Overview of permissions.
 * 
 * @version 7.0.0
 * @since 6.0.0
 * @author gizmore
 */
class Permissions extends MethodQueryTable
{
	use MethodAdmin;
	
	public function gdoTable() { return GDO_Permission::table(); }
	
	public function getPermission() : ?string { return 'staff'; }
	
	public function getTableTitle()
	{
	    return $this->getTitle();
	}
	
	public function getTitle()
	{
	    return t('btn_permissions');
	}

	public function gdoHeaders()
	{
	    $perms = GDO_Permission::table();
		return [
// 			GDT_Count::make(),
			GDT_EditButton::make(),
		    $perms->gdoColumn('perm_name'),
		    $perms->gdoColumn('perm_usercount'),
		    $perms->gdoColumn('perm_level'),
		];
	}
	
	public function beforeExecute() : void
	{
	    $this->renderAdminBar();
	    $this->renderPermissionBar();
	}
	
}
