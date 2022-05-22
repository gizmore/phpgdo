<?php
namespace GDO\Admin\Method;

use GDO\Core\Application;
use GDO\Core\GDT_Hook;
use GDO\Admin\MethodAdmin;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_User;
use GDO\Core\Website;
use GDO\Crypto\BCrypt;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\Form\GDT_DeleteButton;
use GDO\UI\GDT_Page;
use GDO\User\GDT_User;
use GDO\Util\Common;

/**
 * Edit a user.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 3.0.4
 * @see GDO_User
 */
class UserEdit extends MethodForm
{
	use MethodAdmin; # admin protection
	
	public function showInSitemap() { return false; }
	
	/**
	 * @var GDO_User
	 */
	private $user;
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_User::make('user')->notNull(),
	    ];
	}
	
	public function onInit() : void
	{
	    $this->user = $this->getUser();
	}
	
	public function getUser()
	{
	    return GDO_User::table()->getById(Common::getRequestString('user'));
	}
	
	public function beforeExecute() : void
	{
	    if (Application::instance()->isHTML())
	    {
    	    $this->renderNavBar();
    	    $barPermissions = GDT_Bar::make()->horizontal();
    	    $barPermissions->addField(GDT_Link::make('link_edit_permissions')->href(href('Admin', 'PermissionGrant', '&_form[perm_user_id]='.$this->user->getID())));
    	    GDT_Page::instance()->topTabs->addField($barPermissions);
	    }
	}
	
	public function getTitle()
	{
	    $user = $this->getUser();
	    return t('ft_admin_useredit', [$user->renderName()]);
	}
	
	public function createForm(GDT_Form $form) : void
	{
		# Add all columns
	    $table = GDO_User::table();
		foreach ($table->gdoColumnsCache() as $gdt)
		{
			$form->addField($table->gdoColumnCopy($gdt->name));
		}
		
		# Add buttons
		$form->actions()->addField(GDT_Submit::make());
		$form->actions()->addField(GDT_DeleteButton::make());
		$form->addField(GDT_AntiCSRF::make());
		
		# Fill form values with user data
		$form->withGDOValuesFrom($this->getUser());
		
		# Patch columns a bit
		$form->getField('user_name')->pattern(null);
		$form->getField('user_password')->notNull(false)->gdo(null)->initial('');
		$form->getField('user_id')->writable(false);
	}
	
	public function formValidated(GDT_Form $form)
	{
		$values = $form->getFormData();
		$password = $values['user_password'];
		unset($values['user_password']);
		
		$this->user->saveVars($values);
		$form->withGDOValuesFrom($this->user);
		if (!empty($password))
		{
			$this->user->saveVar('user_password', BCrypt::create($password)->__toString());
			return $this->message('msg_user_password_is_now', [$password])->addField(parent::formValidated($form));
		}
		return parent::formValidated($form)->addField($this->renderPage());
	}
	
	public function onSubmit_btn_delete(GDT_Form $form)
	{
		$this->user->delete();
		GDT_Hook::callWithIPC("UserDeleted", $this->user);
		return Website::redirectMessage('msg_user_deleted', [$this->user->renderUserName()], href('Admin', 'Users'));
	}
	
}
