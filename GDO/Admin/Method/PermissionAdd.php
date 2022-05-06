<?php
namespace GDO\Admin\Method;

use GDO\Core\MethodAdmin;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_Permission;

/**
 * Add a new permission.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.1.0
 */
class PermissionAdd extends MethodForm
{
	use MethodAdmin;
	
	public function getPermission() { return 'staff'; }
	
	public function execute()
	{
	    $this->renderPermTabs();
		return parent::execute();
	}
	
	public function createForm(GDT_Form $form)
	{
		$gdo = GDO_Permission::table();
		$form->addFields([
			$gdo->gdoColumn('perm_name'),
		    $gdo->gdoColumn('perm_level'),
			GDT_AntiCSRF::make(),
		]);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form)
	{
		$perm = GDO_Permission::blank($form->getFormData())->insert();
		return $this->message('msg_perm_added', [$perm->displayName()]);
	}
}
