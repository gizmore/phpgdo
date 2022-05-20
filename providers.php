<?php
/**
 * This prints all modules and their providers.
 * The list can be copied by gdo6 authors to Core/ModuleProviders.php
 */
use GDO\File\Filewalker;
use GDO\Util\Regex;

# Use gdo6 core
require "protected/config.php";
require "GDO7.php";

global $mode;

/** @var $argv string **/
$mode = @$argv[1];

if ($mode)
{
	echo "'Captcha' => ['phpgdo-captcha', 'phpgdo-recaptcha2'],\n";
	echo "'DBMS' => ['phpgdo-mysql', 'phpgdo-postgres', 'phpgdo-sqlite],\n";
	echo "'Mailer' => ['phpgdo-mailer-gdo', 'phpgdo-mailer-symfony'],\n";
	echo "'Session' => ['phpgdo-session-db', 'phpgdo-session-cookie'],\n";
}

Filewalker::traverse("GDO", null, false,
function ($entry, $fullpath)
{
	if (is_dir('GDO/' . $entry . "/.git"))
	{
		global $mode;
		$c = file_get_contents('GDO/' . $entry . "/.git/config");
		$c = Regex::firstMatch('#/gizmore/([-_a-z0-9]+)#miD', $c);
		if (str_starts_with($entry, 'phpgdo-'))
		{
			if (!$mode)
			{
				echo "$entry - < https://github.com/gizmore/$c >\n";
			}
			else
			{
				echo "'" . $entry . "' => '$c',\n";
			}
		}
	}
}, 0);


