<?php
namespace GDO\Core\lang;
return [
	'sitename' => 'GDOv7',
	
	# Errors
	'err_db' => "SQL Error %s: %s\nQuery: %s",
	'err_db_no_link' => 'The database connection could not be established.',
	'err_gdo_not_found' => 'The Data for %s with ID: %s could not be found.',
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
	
];
