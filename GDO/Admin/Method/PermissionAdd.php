<?php
declare(strict_types=1);
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_Permission;

/**
 * Add a new permission.
 *
 * @version 7.0.3
 * @since 6.1.0
 * @author gizmore
 */
class PermissionAdd extends MethodForm
{

	use MethodAdmin;

    public function isCLI(): bool { return true; }

	public function getPermission(): ?string { return 'staff'; }

	public function execute(): GDT
	{
		$this->renderPermissionBar();
		return parent::execute();
	}

	protected function createForm(GDT_Form $form): void
	{
		$gdo = GDO_Permission::table();
		$form->addFields(
			$gdo->gdoColumn('perm_name'),
//			$gdo->gdoColumn('perm_level'),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$perm = GDO_Permission::blank($form->getFormVars())->insert();
		return $this->message('msg_perm_added', [$perm->renderName()]);
	}

	public function plugVars(): array
	{
		return [
			['perm_name' => 'New_Perm'],
		];
	}
}
