<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDO;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_EditButton;
use GDO\User\GDO_Permission;

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

    public function isCLI(): bool { return true; }

	public function gdoTable(): GDO { return GDO_Permission::table(); }

	public function getTableTitle(): string
	{
		return $this->getMethodTitle();
	}

	public function getMethodTitle(): string
	{
		return t('btn_permissions');
	}

	public function gdoHeaders(): array
	{
		$perms = GDO_Permission::table();
		return [
			GDT_EditButton::make('edit'),
			$perms->gdoColumn('perm_name'),
			$perms->gdoColumn('perm_usercount'),
//			$perms->gdoColumn('perm_level'),
		];
	}

	public function getPermission(): ?string { return 'staff'; }

	public function onRenderTabs(): void
	{
		$this->renderAdminBar();
		$this->renderPermissionBar();
	}

}
