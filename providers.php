<?php
/**
 * This prints all modules and their providers.
 * The list can be copied by gdo6 authors to Core/ModuleProviders.php
 */
use GDO\File\Filewalker;
use GDO\Util\Regex;

# Use gdo6 core
include "protected/config.php";
include "GDO7.php";

global $mode;

/** @var $argv string **/
$mode = @$argv[1];

if ($mode)
{
	echo "'Captcha' => ['gdo6-captcha', 'gdo6-recaptcha2'],\n";
	echo "'Session' => ['gdo6-session-db', 'gdo6-session-cookie'],\n";
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


