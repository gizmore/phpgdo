<?php
declare(strict_types=1);
namespace GDO\Core\lang;
return [
	# Site
	'sitename' => def('GDO_SITENAME', 'GDOv7'),
	'%s' => '%s',
	'---n/a---' => '---n/a---',
	'module_core' => 'Core',

	# Errors
	'error' => 'Error: %s',
	'err_php_extension' => 'The PHP-Extension `%s`.',
	'err_system_dependency' => 'Module %s has the following unmet requirement: %s',
	'warn_system_dependency' => 'Module %s recommends the following: %s',
	'err_php_major_version' => 'GDOv7 requires PHP version %s.',
	'err_db' => "SQL Error %s: %s\nQuery: %s",
	'err_db_connect' => 'The database connection could not be established: %s.',
	'err_db_no_link' => 'The database connection could not be established.',
	'err_db_ro' => 'The database is in read only mode, and thus cannot write any data.',
	'err_csrf' => 'Your form token is invalid. Your probably have cookie and/or session problems. Try to delete your cookies for this site.',
	'err_gdo_not_found' => 'The Data for %s with ID: %s could not be found.',
	'err_module' => 'Module `%s` is currently not installed.',
	'err_img_not_found' => 'Image not found.',
	'err_unknown_gdo_column' => '´%s´ does not have a column named `%s`.',
	'err_save_unpersisted_entity' => 'An object of type %s shall be saved / updated, but it was not persisted beforehand.',
	'err_create_dir' => 'Cannot create directory ´%s´ in %s line %s.',
	'err_delete_dir' => 'Cannot delete directory ´%s´.',
	'err_no_permission' => 'To utilize %s you need the %s permission.',
	'err_permission_required' => 'You do not have the permissions to execute this function.',
	'err_null_not_allowed' => 'This field may not be left empty.',
	'err_pattern_mismatch' => 'Your input does not match the pattern %s.',
	'err_parameter' => 'A method parameter is errorneous: `%s` - %s',
	'err_unknown_config' => 'Module %s does not have a configuration variable named %s.',
	'err_unknown_user_setting' => 'Module %s does not have a user setting variable named %s.',
	'err_text_only_numeric' => 'Your input is only numeric. This is unexpected.',
	'err_input_not_numeric' => 'This field expects a number.',
	'err_int_bytes_length' => 'The bytesize of this integer is invalid: `%s`.',
	'err_gdo_no_primary_key' => 'This GDT_Object needs a GDO table with primary keys.',
	'err_missing_template' => 'A template file is completely misising: %s.',
	'err_int_not_between' => 'This number has to be between %s and %s.',
	'err_int_too_small' => 'This number has to be larger than or be equal to %s.',
	'err_int_too_large' => 'This number has to be smaller than or be equal to %s.',
	'err_invalid_string_encoding' => 'This text has set an invalid string encoding.',
	'err_properitary_asset_code' => 'You may not access asset files in the GDO folder.',
	'err_invalid_choice' => 'Your selection is invalid.',
	'err_gdt_should_have_a_name' => '%s should have a name!',
	'err_gdo_no_gdt' => 'The GDO `%2$s` does not allow the GDT method `%1$s`.',
	'err_table_gdo' => 'This GDO is not a table object: `%s`.',
	'err_method_disabled' => 'The method %2$s in module %1$s is currently disabled.',
	'err_method_is_stub' => 'This function is not implemented yet: `%s`',
	'err_username_taken' => 'This username is already taken.',
	'err_form_invalid' => 'Your formular is invalid or incomplete. %s errors have been found.',
	'err_unknown_module' => 'The module `%s` is unknown',
	'err_unknown_method' => 'The method `%2$s` is unknown to module `%1$s`.',
	'err_unknown_parameter' => 'Unknown parameter `%s` in method `%s`.',
	'err_user_type' => 'Your user is not of type `%s`.',
	'err_local_url_not_allowed' => 'This url points to local files.',
	'err_external_url_not_allowed' => 'This url points to an external resource: %s',
	'file_not_found' => 'File not Found',
	'err_file_not_found' => 'The file %s could not been found on this server.',
	'err_invalid_gdt_var' => 'Your input is invalid!',
	'forbidden' => 'Forbidden!',
	'err_forbidden' => 'You are not allowed to access this.',
	'err_unknown_field' => 'The field `%s` in this form is either unknown or not writeable.',
	'err_user_required' => 'You need to <a href="%s">signup</a> to continue. You can also <a href="%s">continue as guest</a>',
	'err_select_candidates' => 'There are multiple matches: `%s`.',
	'err_string_length' => 'This text has to be between %s and %s chars in length.',
	'err_unknown_order_column' => 'This column is unknown and cannot be sorted by: `%s`.',
	'err_no_data_yet' => 'There is no data for this item yet.',
	'err_members_only' => 'You need to <a href="%s">authenticate</a> to do this.',
	'err_only_member_access' => 'Only Members are allowed to access this.',
	'err_only_friend_access' => 'Only their friends are allowed to access this.',
	'err_only_private_access' => 'Only the user theyself is allowed to access this.',
	'err_unknown_acl_setting' => 'Unknown ACL Setting: `%s`.',
	'err_submit_without_click_handler' => 'Method `%s`s submit button `%s` is missing a click handler.',
// 	'err_duplicate_field_name' => 'A field has been added twice: `%s`.',
	'err_url_not_reachable' => 'This URL is not reachable: `%s`.',
	'err_cli_form_gdt' => '%s has an error: %s',
	'err_cli_exception' => 'The command caused an error: %s in %s line %s.',
	'err_path_not_exists' => 'The %2$s `%1$s` does not exist or is not readable.',
	'err_token' => 'Your authenticity token is invalid or has been used already.',
	'err_exception' => '%s: `%s`.',
	'err_is_deleted' => 'This entry got deleted and cannot be altered anymore.',
	'err_session_required' => 'You need a session cookie to utilize this method.',
	'err_invalid_ipc' => 'The IPC Bridge settings in config.php are invalid.',
	'err_positional_after_named_parameter' => 'You cannot specify named parameters after required positionals.',
	'err_gdo_is_not_table' => 'A %s table GDO is not a table but an instance.',
	'err_db_unique' => 'This entry already exists.',
	'err_min_max_confusion' => 'The maximum is smaller than the minimum.',
	'err_invalid_user_setting' => 'Module %s has an invalid user setting named %s: %s (%s)',
	'err_gdt_column_define_missing' => 'Your DBMS module cannot create database definitions for the field `%s`, which is a `%s`.',
    'err_no_such_command' => 'The command `%s` is unknown.',

	# err_path
	'is_dir' => 'directory',
	'is_file' => 'file',

	# Messages
	'msg_form_validated' => 'Your form has been sent successfully.',
	'msg_cache_flushed' => 'All caches have been flushed. E.g; rm -rf temp/, Cache::flush(), Internal caches via hooks.',
	'msg_crud_created' => 'Your %s has been created successfully.',
	'msg_crud_updated' => 'Your %s has been updated successfully.',
	'msg_binary_detected' => 'The %s binary has been detected.',
	'msg_module_methods' => '%s methods: %s.',
	'msg_abort' => 'User abort.',

	# Checkbox
	'enum_yes' => 'Yes',
	'enum_no' => 'No',
	'enum_undetermined_yes_no' => 'undecided',

	# Enum
	'enum_none' => '-None-',
	'enum_all' => 'All',
	'enum_staff' => 'Staff',
	'enum_unknown' => 'unknown',

	# E-Mail
	'enum_html' => 'HTML',
	'enum_text' => 'Text',
	'email_fmt' => 'Format',

	# Navpos
	'enum_left' => 'Left',
	'enum_right' => 'Right',
	'enum_bottom' => 'Bottom',

	# Permissions
	'sel_no_permissions' => 'Not required',
	'perm_admin' => 'Admin',
	'perm_staff' => 'Staff',
	'perm_cronjob' => 'Cronjob',

	# Buttons
	'btn_ok' => 'O.K.',
	'btn_abort' => 'Abort',
	'btn_add' => 'Add',
	'btn_back' => 'Back',
	'btn_clearcache' => 'Clear Cache',
	'btn_create' => 'Create',
	'btn_delete' => 'Delete',
	'btn_edit' => 'Edit',
	'btn_modules' => 'Modules',
	'btn_overview' => 'Overview',
	'btn_participate' => 'Participate',
	'btn_preview' => 'Preview',
	'btn_print' => 'Print',
	'btn_save' => 'Save',
	'btn_send' => 'Send',
	'btn_invisible' => 'Set Invisble',
	'btn_send_mail' => 'Send Mail',
	'btn_set' => 'Set',
	'btn_upload' => 'Upload',
	'btn_view' => 'View',
	'btn_visible' => 'Set Visible',
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
	'unknown_user' => 'Unknown User',

	# GDTs
    'all' => 'All',
    'expires' => 'Expires At',
	'website_content' => 'Website Content',
	'exception' => 'Exception',
	'reason' => 'Reason',
	'code' => 'Code',
	'front' => 'Front',
	'back' => 'Back',
	'redirect' => 'Redirect',
	'attachment' => 'Attachment',
	'edited_by' => 'Edited by',
	'html' => 'HTML',
	'format' => 'Format',
	'ghost' => 'Ghost',
	'guest' => 'Guest',
	'last_url' => 'Last URL',
	'age' => 'Age',
	'file_size' => 'Filesize',
	'folder' => 'Folder',
	'message' => 'Message',
	'url' => 'URL',
	'filesize' => 'Size',
	'file_type' => 'Type',
	'module_path' => 'Path',
	'sorting' => 'Sorting',
	'enabled' => 'Enabled',
	'name' => 'Name',
	'user_type' => 'User Type',
	'user_guest_name' => 'Guest Name',
	'user_level' => 'Level',
	'copyright' => 'Copyright',
	'password' => 'Password',
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
	'editor' => 'Editor',
	'quote_by' => 'Quote by %s',
	'quote_at' => 'At %s',
	'not_specified' => 'Not Specified',
	'email' => 'E-Mail',
	'size' => 'Size',
	'object_filter' => 'Filter',
	'directory' => 'Directory',
	'type' => 'Type',
	'print' => 'Print',
	'favorite_color' => 'Favorite Color',
	'website' => 'Website',
	'information' => 'Information',
	'health' => 'Health',
	'logo' => 'Logo',
	'admin' => 'Administration',
	'approve' => 'Accept',
	'captcha' => 'Captcha',

	# CBX
	'sel_all' => 'Select All',
	'sel_checked' => 'Yes',
	'sel_unchecked' => 'No',

	# Fineprint
	'privacy' => 'Privacy',
	'impressum' => 'Impressum',
	'md_core_privacy' => 'Privacy and Dataflow information for %s.',
	'md_core_impressum' => 'The impressum for the %s service website.',

	# Util
	'or' => 'or',
	'and' => 'and',
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

	# List
	'lbl_search_criteria' => 'Search: %s',
	'order_by' => 'Order By',
	'order_dir' => 'Direction',
	'asc' => 'Ascending',
	'desc' => 'Descending',

	# User
	'users' => 'Users',
	'permissions' => 'Permissions',

	'msg_sort_success' => 'Successfully sorted?!',

	### Config ###
	'cfg_allow_editor_choice' => 'Allow Texteditor Choice?',
	'cfg_allow_javascript' => 'Allow JavaScript?',
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
	'cfg_dotfiles' => 'Allow to read and index hidden dotfiles?',

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
Referrer: %s
	
Kind Regards,
The %2$s System',

	'confirm_delete' => 'Do you really want to delete this?',

	'gdt_redirect_to' => 'Redirecting to %s...',

	'unknown_permission' => 'This permission is unknown',
	'add_permissions' => 'Add a permisison',

	'mt_sort' => 'Sort %s Database',

	'mt_crud_create' => 'New %s',
	'mt_crud_update' => 'Edit %s',

	'method' => 'Method',

	'msg_installed_modules' => 'Installed modules: %s.',

	'mt_core_security' => 'SECURITY.md',
	'mt_core_robots' => 'robots.txt',
	'mt_core_gettypes' => 'Get GDT Meta-Data',
	'mt_core_pathcompletion' => 'Path completion',
	'mt_ajax' => '%s Data Retrieval',

	'creator_header' => '%s added this %s.',

	'please_choose' => 'Please choose...',
	'required' => 'This field is required and has to be filled out.',

	# Privacy
	'info_privacy_related_module' => '%s is a privacy related module that is currently enabled.',
	't_privacy_core_toggles' => 'Core Configuration',

	'privacy_settings' => 'Privacy Settings',
	'mt_core_forcessl' => 'Force Encryption',

	'err_nothing_happened' => 'No error occured, but unexpectedly nothing happened.',

	# Health
	'gdo_revision' => 'GDO-Revision',
	'health_cpus' => 'Cores',
	'health_load' => 'Load',
	'health_clock' => 'GHz',
	'health_mem' => 'Memory',
	'health_free' => 'Free',
	'health_used' => 'Used',
	'health_hdd_avail' => 'HDD',
	'health_hdd_free' => 'Free',
	'health_hdd_used' => 'Used',

	'edited_info' => 'Edited by %s %s ago',

	'md_on_windows' => 'Run this script to install phpgdo on windows. Caution: git4windows and PHP8.0 is required. Warning: Do not run untrusted code!',
	'menu_admin' => 'Admin',
	'menu_profile' => 'Profile',
	'level_spent' => 'Used Level',
	'tt_level_spent' => 'Your spent amount of user level on site features.',
	'md_impressum' => 'The impressum on %s. Who is responsible for it\'s content?',

	# 7.0.3
	'force_ssl' => 'force TLS?',
	'log_request' => 'log every Request?',
	'sess_https' => 'TLS Session only?',
	'sess_samesite' => 'Session SameSite behaviour',
	'mobile' => 'Mobile',
	'filesize ' => 'Filesize',
	'_filesize' => [
		'B',
		'KB',
		'MB',
		'GB',
		'TB',
		'PB',
	],
	'text_editor' => 'Editor',
	'cfg_default_editor' => 'Default Editor',
	'cfg_fav_color' => 'Favorite Color',
	'section' => 'Section',
    'member' => 'Member',
    'staff' => 'Staff Member',
    'administrator' => 'Administrator',
    'device_width' => 'Device Width',
    'device_height' => 'Device Height',
    'device_version' => 'Device Version',

    'toggle_sidebar' => 'Toggle Sidebar',
    'mt_core_fileserver' => 'Fileserver',
    'privacy_info_ui_module' => 'User Interface Settings',
];
