<?php
namespace GDO\Crypto\lang;

return [
	'module_crypto' => 'Kryptografie',
	'cfg_password_strong' => 'Starkes Passwort erzwingen?',
	'tt_cfg_password_strong' => 'Setzt nur die Mindestlänge auf 9. Mit Pepper und Salt ist das gut genug.',
	'cfg_bcrypt_cost' => 'Passwortsicherheit',
	'tt_cfg_bcrypt_cost' => 'BCrypt-Kosten zwischen 1 und 11.',
	'tt_password' => 'Dein Passwort muss mindestens 4 Zeichen lang sein.<br/>Verwende wichtige Passwörter nicht erneut.',
	'err_pass_too_short' => 'Ein Passwort muss mindestens 4 Zeichen lang sein.',
	'err_strong_pass' => 'Ein Passwort muss mindestens 8 Zeichen lang sein. Es muss eine Zahl und ein Sonderzeichen enthalten. Es darf nicht mit einem Ausrufezeichen enden.',
	'info_crypto_hash_algo' => 'Auf %s speichern wir dein Passwort nur gehasht mit dem BCrypt-Algorithmus.<br/>Die Hashes werden mit Pepper und Salt versehen; die BCrypt-Kosten sind auf %s gesetzt.',
];
