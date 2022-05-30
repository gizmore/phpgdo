<?php
namespace GDO\Core\lang;
return [
	# Site
	'sitename' => def('GDO_SITENAME', 'GDOv7'),
	
	# Errors
	'err_db' => "SQL Error %s: %s\nQuery: %s",
	'err_db_connect' => 'The database connection could not be established: %s.',
	'err_db_no_link' => 'The database connection could not be established.',
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
	'err_unknown_method' => 'The method `%2$s` is unknown to module `%1$s`.',
	'err_unknown_parameter' => 'Unknown parameter `%s` in method `%s`.',
	'err_user_type' => 'Your user is not of type %s.',
	'err_external_url_not_allowed' => 'This url may not point to an external resource: %s',
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
	'btn_edit' => 'Edit',
	'btn_save' => 'Save',
	'btn_send' => 'Send',
	'btn_set' => 'Set',
	'submit' => 'Submit',
	
	# Float
	'decimal_point' => '.',
	'thousands_seperator' => ',',
	
	# UserType
	'guest' => 'Guest',
	'member' => 'Member',

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
	'ipp' => 'IPP',
	
	# Fineprint
	'privacy' => 'Privacy',
	'impressum' => 'Impressum',
	
	# Util
	'and' => ' and ',
	
	# Welcome
	'welcome' => 'Welcome',
	
	# Version
	'php_version' => 'PHP Version',
	'gdo_version' => 'GDO Version',
	
	# Directory Index
	'ft_dir_index' => '%s (%s files and folders)',
	'ft_filenotfound' => 'Not Found!',
	'ft_notallowed' => 'Forbidden!',
	
	# Table
	'cfg_spr' => 'Suggestions per Request',
	'cfg_ipp_cli' => 'Items per page (CLI)',
	'cfg_ipp_http' => 'Items per page (HTML)',
	
	# User
	'users' => 'Users',
	'permissions' => 'Permissions',
	
	'msg_sort_success' => 'Successfully sorted?!',
];
