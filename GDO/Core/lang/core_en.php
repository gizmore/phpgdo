<?php
namespace GDO\Core\lang;
return [
	# Site
	'sitename' => def('GDO_SITENAME', 'GDOv7'),
	
	# Errors
	'err_php_extension_missing' => 'This module requires the %s PHP extension.',
	'err_system_dependency' => 'A system requirement is not fulfilled: %s',
	'err_php_major_version' => 'GDOv7 requires PHP version %s.',
	'err_db' => "SQL Error %s: %s\nQuery: %s",
	'err_db_connect' => 'The database connection could not be established: %s.',
	'err_db_no_link' => 'The database connection could not be established.',
	'err_csrf' => 'Your form token is invalid. Your probably have cookie and/or session problems. Try to delete your cookies for this site.',
	'err_gdo_not_found' => 'The Data for %s with ID: %s could not be found.',
	'err_module' => 'Module `%s` is currently not installed.',
	'err_img_not_found' => 'Image not found.',
	'err_unknown_gdo_column' => '´%s´ does not have a column named `%s`.',
	'err_save_unpersisted_entity' => 'An object of type %s shall be saved / updated, but it was not persisted beforehand.',
	'err_create_dir' => 'Cannot create directory ´%s´ in %s line %s.',
	'err_no_permission' => 'To utilize %s you need the %s permission.',
	'err_null_not_allowed' => 'This field may not be left empty.',
	'err_pattern_mismatch' => 'Your input does not match the pattern %s.',
	'err_parameter' => 'A method parameter is errorneous: %s - %s',
	'err_unknown_config' => 'Module %s does not have a configuration variable named %s.',
	'err_unknown_user_setting' => 'Module %s does not have a user settig variable named %s.',
	'err_text_only_numeric' => 'Your input is only numeric. This is unexpected.',
	'err_input_not_numeric' => 'This field expects a number.',
	'err_int_bytes_length' => 'The bytesize of this integer is invalid: %s.',
	'err_gdo_no_primary_key' => 'This GDT_Object needs a GDO table with primary keys.',
	'err_missing_template' => 'A template file is completely misising: %s.',
	'err_int_not_between' => 'This number has to be between %s and %s.',
	'err_int_too_small' => 'This number has to be larger than or be equal to %s.',
	'err_int_too_large' => 'This number has to be smaller than or be equal to %s.',
	'err_invalid_string_encoding' => 'This text has set an invalid string encoding.',
	'err_properitary_asset_code' => 'You may not access asset files in the GDO folder.',
	'err_invalid_choice' => 'Your selection is invalid.',
	'err_gdt_should_have_a_name' => '%s should have a name!',
	'err_permission_required' => 'You need the %s permission to execute this function.',
	'err_gdo_no_gdt' => 'The GDO `%2$s` does not allow the GDT method `%1$s`.',
	'err_table_gdo' => 'This GDO is not a table object: %s.',
	'err_method_disabled' => 'The method %2$s in module %1$s is currently disabled.',
	'err_method_is_stub' => 'This function is not implemented yet: %s',
	'err_username_taken' => 'This username is already taken.',
	'err_form_invalid' => 'Your formular is invalid or incomplete. %s errors have been found.',
	'err_unknown_module' => 'The module `%s` is unknown',
	'err_unknown_method' => 'The method `%2$s` is unknown to module `%1$s`.',
	'err_unknown_parameter' => 'Unknown parameter `%s` in method `%s`.',
	'err_user_type' => 'Your user is not of type %s.',
	'err_local_url_not_allowed' => 'This url may not point to local files.',
	'err_external_url_not_allowed' => 'This url may not point to an external resource: %s',
	'file_not_found' => 'File not Found',
	'err_file_not_found' => 'The file %s could not been found on this server.',
	'err_invalid_gdt_var' => 'Your input is invalid!',
	'forbidden' => 'Forbidden!',
	'err_forbidden' => 'You are not allowed to access this.',
	'err_unknown_field' => 'The field `%s` in this form is either unknown or not writeable.',
	'err_user_required' => 'You need to signup to continue. You can also <a href="%s">continue as guest</a>',
	'err_select_candidates' => 'There are multiple matches: %s',

	# Messages
	'msg_form_validated' => 'Your form has been sent successfully.',

	# Checkbox
	'enum_yes' => 'Yes',
	'enum_no' => 'No',
	
	# Enum
	'enum_none' => '-None-',
	
	# Permissions
	'perm_admin' => 'Admin',
	'perm_staff' => 'Staff',
	'perm_cronjob' => 'Cronjob',
	
	# Buttons
	'btn_back' => 'Back',
	'btn_delete' => 'Delete',
	'btn_edit' => 'Edit',
	'btn_save' => 'Save',
	'btn_send' => 'Send',
	'btn_set' => 'Set',
	'btn_upload' => 'Upload',
	'submit' => 'Submit',
	
	# Float
	'decimal_point' => '.',
	'thousands_seperator' => ',',
	
	# UserType
	'enum_system' => 'System',
	'enum_ghost' => 'Ghost',
	'enum_guest' => 'Guest',
	'enum_member' => 'Member',
	'enum_link' => 'Link',
	'enum_bot' => 'Bot',
	
	# GDTs
	'file_size' => 'Filesize',
	'message' => 'Message',
	'password' => 'Password',
	'url' => 'URL',
	'filesize' => 'Size',
	'file_type' => 'Type',
	'module_path' => 'Path',
	'sorting' => 'Sorting',
	'enabled' => 'Enabled',
	'name' => 'Name',
	'user_type' => 'User Type',
	'user_guest_name' => 'Guest Name',
	'level' => 'Level',
	'copyright' => 'Copyright',
	'ipp' => 'IPP',
	'keywords' => 'Keywords',
	'description' => 'Description',
	'title' => 'Title',
	'cfg_hook_sidebar' => 'Hook in Sidebar?',
	'text' => 'Text',
	'string' => 'String',
	'xsrf' => 'XSRF Protection',
	'permission' => 'Permission',
	'user' => 'User',
	'username' => 'Username',
	'edited_at' => 'Edited at',
	'deleted_at' => 'Deleted at',
	'deleted_by' => 'Deleted by',
	'unknown' => 'Unknown',
	'id' => 'ID',
	'testfield' => 'Testfield',
	'created_at' => 'Created at',
	'created_by' => 'Created by',
	'page' => 'Page',
	'search' => 'Search',
	'path' => 'Path',
	'font' => 'Font',
	'color' => 'Color',
	'priority' => 'Priority',
	'from' => 'From',
	'to' => 'To',
	'version' => 'Version',
	'count' => 'Count',
	'backup_file' => 'Backup File',
	'license' => 'License',
	'step' => 'Step',
	'ip' => 'IP',
	'token' => 'Token',
	
	# CBX
	'sel_all' => 'Select All',
	'sel_checked' => 'Entries with Yes',
	'sel_unchecked' => 'Entries with No',
	
	# Fineprint
	'privacy' => 'Privacy',
	'md_privacy' => 'Privacy and Dataflow information for %s.',
	'impressum' => 'Impressum',
	'md_impressum' => 'The impressum for the %s service website.',
	
	# Util
	'and' => ' and ',
	'none' => 'None',
	
	# Welcome
	'welcome' => 'Welcome',
	'md_welcome' => 'The welcome page for the %s service.',
	
	# Version
	'info_version' => 'Display the GDOv7 and PHP version.',
	'php_version' => 'PHP Version',
	'gdo_version' => 'GDO Version',
	
	# Directory Index
	'mt_dir_index' => '%s (%s files and folders)',
	'mt_filenotfound' => 'Not Found!',
	'mt_notallowed' => 'Forbidden!',
	
	# Table
	'cfg_spr' => 'Suggestions per Request',
	'cfg_ipp_cli' => 'Items per page (CLI)',
	'cfg_ipp_http' => 'Items per page (HTML)',
	
	# User
	'users' => 'Users',
	'permissions' => 'Permissions',
	
	'msg_sort_success' => 'Successfully sorted?!',

	### Config ###
	'cfg_asset_revision' => 'Asset revision / Client Cache poisoning',
	'cfg_system_user' => 'System User',
	'cfg_show_impressum' => 'Show impressum in the footer?',
	'cfg_show_privacy' => 'Show privacy information in the footer?',
	'cfg_allow_guests' => 'Enable GDOv7 Guestuser System?',
	'cfg_siteshort_title_append' => 'Append Site Shortname in page titles?',
	'cfg_mail_403' => 'Send mail on 403 errors?',
	'cfg_mail_404' => 'Send mail on 404 errors?',
	'cfg_directory_indexing' => 'Enable Directory Indexing?',
	'cfg_module_assets' => 'Allow assets to be loaded from the GDO source directory?',
	
	### 403 ###
	'mail_title_403' => '%s: 403 (%s)',
	'mail_body_403' => '
Dear %s,
	
There has been visited an forbidden URL on %s.
URL: %s
User: %s
	
Kind Regards,
The %2$s System',
	
	### 404 ###
	'mail_title_404' => '%s: 404 (%s)',
	'mail_body_404' => '
Dear %s,
	
There has been visited an unknown URL on %s.
URL: %s
User: %s
	
Kind Regards,
The %2$s System',

# NEw	
	'confirm_delete' => 'Do you really want to delete this?',
	
	'md_switch_language' => 'Switch language',
	'gdt_redirect_to' => 'Redirecting to %s...',

	'unknown_permission' => 'This permission is unknown',
	'add_permissions' => 'Add a permisison',

	
];
