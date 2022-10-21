<?php
namespace GDO\Crypto\lang;
return [
	'module_crypto' => 'Cryptography',
	'cfg_password_strong' => 'Force a strong password?',
	'tt_cfg_password_strong' => 'This only sets the minimum length to 9. Good enough with pepper and salt.',
	'cfg_bcrypt_cost' => 'Password security',
	'tt_cfg_bcrypt_cost' => 'BCrypt cost between 1 and 11.',
	'tt_password' => 'Your password has to be at least 4 characters in length.<br/>Do not re-use important passwords.',
	'err_pass_too_short' => 'A password has to be at least 4 characters in length.',
	'err_strong_pass' => 'A password has to be at least 8 characters. It has to contain a number and a special char. There may be no exclamation mark at the end.',
	'info_crypto_hash_algo' => 'On %s we only store your password hashed, using the BCrypt algo.<br/>The hashes are peppered and salted, and the BCrypt hash algorithm cost is set to %s.',
];
