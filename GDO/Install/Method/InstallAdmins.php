<?php
namespace GDO\Install\Method;

use GDO\DB\Database;
use GDO\Form\MethodForm;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\Core\Debug;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_UserPermission;
use GDO\User\GDO_Permission;
use GDO\Core\ModuleLoader;
use GDO\Crypto\BCrypt;
use GDO\Session\GDO_Session;
use GDO\Core\GDT;
use GDO\Crypto\GDT_PasswordHash;

/**
 * Install an admin account.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.5
 */
class InstallAdmins extends MethodForm
{
	public function getMethodTitle() : string
	{
		return t('install_title_6');
	}
	
	public function getMethodDescription() : string
	{
		return $this->getMethodTitle();
	}
	
	public function createForm(GDT_Form $form) : void
	{
		Debug::init();
		Database::init();
		GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);
		$hasdb = GDO_DB_HOST !== null;
		ModuleLoader::instance()->loadModules($hasdb, !$hasdb);
		$users = GDO_User::table();
		$form->text('info_install_admins');
		$form->addFields(
			$users->gdoColumn('user_name')->exists(false),
			$users->gdoColumn('user_password'),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function renderPage() : GDT
	{
		return GDT_Template::templatePHP('Install', 'page/installadmins.php', ['form' => $this->getForm()]);
	}
	
	public function formValidated(GDT_Form $form)
	{
		/** @var $password GDT_PasswordHash **/
		$password = $form->getField('user_password');
		$password->input(BCrypt::create($password->getVar())->__toString());
		
		$user = GDO_User::blank($this->getInputs());
		$user->setVar('user_type', 'member');
		$user->setVar('user_password', $password->getHash());
		$user->insert();
		
		$permissions = ['admin' => 1000, 'staff' => 500, 'cronjob' => 500];
		foreach ($permissions as $permission => $level)
		{
			GDO_UserPermission::grantPermission($user, GDO_Permission::create($permission, $level));
		}
		
		return parent::formValidated($form);
	}
	
}
