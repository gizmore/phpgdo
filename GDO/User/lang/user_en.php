<?php
declare(strict_types=1);
namespace GDO\User\lang;
return [
	'module_user' => 'User',
	'gdo_user' => 'User',
	'gdo_session' => 'Session',
	'gender' => 'Gender',
	'profile_views' => 'Profile Views',
	'profile_visibility' => 'Profile visible for',
	'probably_malicious' => 'Probably Malicious',

	'cfg_acl_levels' => 'Visibility via Levels?',
	'cfg_acl_permissions' => 'Visibility via Permissions?',
	'cfg_acl_relations' => 'Visibility via Relations?',

	'enum_none' => 'not specified',
	'enum_male' => 'male',
	'enum_female' => 'female',

	'registered_at' => 'Registered at',
	'last_activity' => 'Last activity',

	'user_name' => 'Username',
	'profile_level' => 'This %s has a user level of %s.',

	'link_your_profile' => '[%s]',

	'tt_password' => 'Your password should be at least 9 chars and not be included in dictionaries.',

	'lvlpopup_item_level' => 'Access level: %s',
	'lvlpopup_your_level' => 'Your level: %s',
	'lvlpopup_ok' => 'Your userlevel is compliant with the requirements.',
	'lvlpopup_too_low' => 'You are not allowed to access this item.',

	'link_user_add_user' => 'Create a new user',

	'md_user_adduser' => 'Create a new user',
	'md_user_admin' => 'Administrate users',

	'user_subtitle' => 'Member since %s. Level: %s',
	'user_info_subtitle' => '%s is %s on %s with userlevel %s.',

	'mt_user_cleanupguests' => 'Guest Cleanup',
	'md_user_cleanupguests' => 'Guest Cleanup Cronjob for GDOv7.',

	'mt_user_profile' => '%s\'s Profile',
	'md_user_profile' => '%s\'s Profile on %s. %s',
	'tt_user_level' => '%s holds userlevel %s.',

	'lbl_acl_level' => '`%s` requires level',
	'lbl_acl_relation' => '`%s` visible for',
	'lbl_acl_permission' => '`%s` requires permission',

	'enum_acl_hidden' => 'Hidden',
	'enum_acl_noone' => 'Noone',
	'enum_acl_friends' => 'Friends',
	'enum_acl_friend_friends' => 'Friend Friends',
	'enum_acl_members' => 'Members',
	'enum_acl_guests' => 'Guests',
	'enum_acl_all' => 'All',

	'lbl_own_acl_level' => '%s Level',
	'lbl_own_acl_relation' => '%s Usertype',
	'lbl_own_acl_permission' => '%s Permission',

	'err_only_user_access' => 'Only users can see this',
	'err_only_friend_friend_access' => 'Sie müssen zumindest Freundesfreund sein um dies zu sehen.',
	'err_only_level_access' => 'You need level %s to see this.',

	'link_account' => 'Your account',
	'p_info_own_profile' => 'This is your own Profile. You can edit it here: %s',
	'err_user_deleted' => 'This user got deleted by %s on %s.',
];
