<?php
namespace GDO\Admin\lang;

return [
	'list_admin_modules' => '%s Modules',
	'list_admin_users' => 'Admin overview of %s users',
################################################################
	'btn_admin' => 'Admin',
	'btn_phpinfo' => 'PHPInfo',
	'btn_users' => 'Users',
	'btn_permissions' => 'Permissions',
	'btn_cronjob' => 'Cronjob',
	'btn_login_as' => 'Login as…',
	'btn_reinstall' => 'Reinstall',
################################################################
	'version_db' => 'Vers.',
	'version_fs' => 'V.FS.',
	'btn_install' => 'Install',
	'btn_configure' => 'Configure',
	'btn_uninstall' => 'Wipe',
	'btn_enable' => 'Enable',
	'btn_disable' => 'Disable',
	'msg_module_enabled' => 'The %s module has been enabled.',
	'msg_module_disabled' => 'The %s module has been disabled.',
################################################################
	'mt_admin_install' => 'Install %s',
	'msg_module_installed' => 'The %s module has been successfully installed.',
	'msg_module_uninstalled' => 'The %s has been wiped from disk. All database records should have been destroyed.',
################################################################
	'mt_admin_configure' => 'Configure %s',
	'module_version' => 'Version',
	'version_available' => 'Version available',
	'form_div_config_vars' => 'Configuration Variables',
	'msg_module_saved' => 'Config has been saved. %s',
	'msg_modulevar_changed' => '`%s` changed from %s to %s.',
	'href_admin' => 'Admin section',
	'confirm_wipe_module' => 'Do you want to drop these tables: %s?',
################################################################
	'mt_admin_useredit' => 'Edit user ´%s´',
	'msg_user_password_is_now' => 'The user`s password is now: %s',
################################################################
	'link_add_perm' => 'Add Permission',
	'link_edit_permissions' => 'Edit Permissions',
	'link_grant_perm' => 'Grant Permission',
	'link_revoke_perm' => 'Revoke Permission',
	'mt_admin_permissionadd' => 'Add Permission',
	'user_count' => 'Members',
	'perm_revoke' => 'Revoke',
	'msg_perm_added' => 'A new permission has been added: %s',
################################################################
	'mt_admin_permissionrevoke' => 'Revoke Permission',
################################################################
	'mt_admin_permissiongrant' => 'Grant Permission',
	'choose_permission' => 'Choose permission',
	'msg_perm_granted' => 'You have granted %s permissions to %s.',
################################################################
	'link_create_user' => 'Create new user',
	'mt_admin_usercreate' => 'Create a new user',
################################################################
	'md_admin_modules' => 'Administrate modules',
	'admin_user_created' => 'The user has been created successfully.',
	'link_user_edit' => 'Edit user',
	'md_admin_configure' => 'Configure the %s module',
################################################################
	'msg_perm_revoked' => 'The permission has been revoked.',
	'info_module_deps' => 'Dependencies: %s',
	'info_module_freps' => 'Suggested modules: %s',
	'mt_admin_clearcache' => 'Clear Cache',
################################################################
	'list_admin_viewpermission' => '%s Users got this permission',
	'perm_add' => 'Add Permission',
	'msg_user_deleted' => 'The user %s has been marked as deleted.',

	####
	'err_mod_config' => 'The %s module configurations and settings are faulty. From %s checked GDT, %s were errorneous. From %s entries, %s have have been removed to fix them.',
	'err_mod_config_error' => 'The %s module value `%s` for %s is invalid: %s - Example: `%s`.',
	'msg_mod_config_fixed' => 'The %s module value `%s` for %s was invalid: %s. I have reset it to the default, `%s`.',
	'msg_mod_config_ok' => 'The %s module configurations and settings are fine. For %s GDT, in total %s entries were checked.',

];
