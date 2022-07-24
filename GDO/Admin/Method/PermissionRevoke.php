<?php
namespace GDO\Admin\Method;

use GDO\Admin\MethodAdmin;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDT_Permission;
use GDO\User\GDT_User;
use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\Util\Common;

/**
 * Revoke a permission.
 * @version 6.10.1
 * @since 3.1.0
 * @author gizmore
 */
class PermissionRevoke extends MethodForm
{
	use MethodAdmin;
	
	public function getPermission() : ?string { return 'admin'; }
	
	/**
	 * @var GDO_User
	 */
	private $user;
	
	/**
	 * @var GDO_Permission
	 */
	private $permission;
	
	public function onInit()
	{
	    if ($userid = Common::getRequestString('user'))
	    {
	        $this->user = GDO_User::table()->getById($userid);
	    }
	    if ($permid = Common::getRequestString('perm'))
	    {
	        $this->permission = GDO_Permission::table()->getById($permid);
	    }
	}
	
	public function execute()
	{
	    $this->renderPermissionBar();
		return parent::execute();
	}
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			GDT_User::make('perm_user_id')->notNull()->initial($this->user ? $this->user->getID() : '0'),
			GDT_Permission::make('perm_perm_id')->notNull()->initial($this->permission ? $this->permission->getID() : '0'),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	/**
	 * Revoke a permission.
	 * {@inheritDoc}
	 * @see \GDO\Form\MethodForm::formValidated()
	 */
	public function formValidated(GDT_Form $form)
	{
	    /** @var $user GDO_User **/
	    /** @var $perm GDO_Permission **/
		$user = $form->getFormValue('perm_user_id');
		$perm = $form->getFormValue('perm_perm_id');
		
		$condition = sprintf('perm_user_id=%s AND perm_perm_id=%s',
		    $user->getID(), $perm->getID());
		
		$affected = GDO_UserPermission::table()->deleteWhere($condition);
		
		if ($affected)
		{
    		$user->changedPermissions();
		}
		
		$response = $affected > 0 ? $this->message('msg_perm_revoked') :
		  $this->error('err_nothing_happened');
		return $response->addField($this->renderPage());
	}
}
