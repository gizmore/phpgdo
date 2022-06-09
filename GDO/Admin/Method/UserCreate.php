<?php
namespace GDO\Admin\Method;

use GDO\Form\MethodForm;
use GDO\Form\GDT_Form;
use GDO\User\GDO_User;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\Core\GDT_Hook;
use GDO\Admin\MethodAdmin;
use GDO\UI\GDT_EditButton;
use GDO\User\GDT_Username;

/**
 * Manually create a user.
 * Only specify user_name, the rest can be done via UserEdit.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.1.2
 */
final class UserCreate extends MethodForm
{
    use MethodAdmin;
    
    public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			GDT_Username::make('user_name')->notNull()->exists(false),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$user = GDO_User::blank([
			'user_type' => 'member',
			'user_name' => $form->getFormVar('user_name'),
		])->insert();
		GDT_Hook::callWithIPC('UserActivated', $user, null);
		$linkEdit = GDT_EditButton::make('link_user_edit')->href(href('Admin', 'UserEdit', '&user='.$user->getID()));
		return $this->message('admin_user_created')->addField($linkEdit);
	}
	
}
