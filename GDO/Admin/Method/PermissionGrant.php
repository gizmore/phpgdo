<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;

class PermissionGrant extends MethodForm
{
	use MethodAdmin;
	
	public function beforeExecute()
	{
	    $this->renderNavBar();
	    $this->renderPermTabs();
	}
	
	public function createForm(GDT_Form $form) : void
	{
		$gdo = GDO_UserPermission::table();
		$form->addFields(
			$gdo->gdoColumn('perm_user_id'),
			$gdo->gdoColumn('perm_perm_id')->notNull()->emptyInitial(t('choose_permission')),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$userpermission = GDO_UserPermission::blank($form->getFormData())->replace();
		$permission = $userpermission->getPermission();
		/** @var $permission GDO_Permission **/
		$permission = $form->getFormValue('perm_perm_id');
		/** @var $user GDO_User **/
		$user = $form->getFormValue('perm_user_id');
		$user->changedPermissions();
		$this->resetForm();
		return $this->message('msg_perm_granted', [$permission->displayName(), $user->displayNameLabel()])->addField($this->renderPage());
	}
	
}
