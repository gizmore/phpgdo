<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_User;
use GDO\Crypto\BCrypt;
use GDO\UI\GDT_DeleteButton;
use GDO\User\GDT_User;
use GDO\Crypto\GDT_Password;

/**
 * Edit a user. Beside level, password and deletion, nothing much can be changed.
 * 
 * @TODO To edit user config and settings, a new module has to be written (or account settings need a god mode).
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 3.0.4
 * @see GDO_User
 */
class UserEdit extends MethodForm
{
	use MethodAdmin; # admin protection
	
	public function isTrivial() : bool { return false; }
	
	public function getMethodTitle() : string
	{
		return t('mt_admin_useredit', [$this->getUser()->renderUserName()]);
	}
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_User::make('user')->deleted()->notNull(),
	    ];
	}
	
	public function getUser() : GDO_User
	{
		return $this->gdoParameterValue('user');
	}
	
	public function onRenderTabs() : void
	{
   	    $this->renderAdminBar();
   	    $this->renderPermissionBar();
	}

	public function createForm(GDT_Form $form) : void
	{
		# Add all columns
	    $table = GDO_User::table();
	    $user = $this->getUser();
	    $form->gdo($user);
		foreach ($table->gdoColumnsCache() as $gdt)
		{
			if ($name = $gdt->getName())
			{
				$form->addField($user->gdoColumn($name));
			}
		}
		
		# Add buttons
		$form->actions()->addField(GDT_Submit::make());
		$form->actions()->addField(GDT_DeleteButton::make()->onclick([$this, 'onDeleteUser']));
		$form->addField(GDT_AntiCSRF::make());
		
		# Patch columns a bit
		$form->getField('user_name')->noPattern(null);
		$form->addField(GDT_Password::make('user_password')->notNull(false)->initial(''));
	}
	
	public function formValidated(GDT_Form $form)
	{
		$user = $this->getUser();
		$values = $form->getFormVars();
		$password = $values['user_password'];
		unset($values['user_password']);
		$user->saveVars($values);
		if (!empty($password))
		{
			$user->saveSettingVar('Login', 'password', BCrypt::create($password)->__toString());
			return $this->message('msg_user_password_is_now', [$password])->addField(parent::formValidated($form));
		}
		return parent::formValidated($form);
	}
	
	public function onDeleteUser()
	{
		$user = $this->getUser();
		$user->markDeleted();
		$href = href('Admin', 'Users');
		return $this->redirectMessage('msg_user_deleted', [$user->renderUserName()], $href);
	}
	
}
