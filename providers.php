<?php
declare(strict_types=1);

/**
 * This prints all modules and their providers.
 * The list can be copied by authors to Core/ModuleProviders.php
 */

use GDO\Util\Filewalker;
use GDO\Util\Regex;

# Bootstrap GDOv7
require 'protected/config.php';
require 'GDO7.php';

global $mode;

$mode = isset($argv[1]);

/**
 * Manually manage multi-providers.
 */
$multi = [
	'Captcha' => ['phpgdo-captcha', 'phpgdo-recaptcha2'],
	'DBMS' => ['phpgdo-mysql', 'phpgdo-postgres', 'phpgdo-sqlite'],
	'Mailer' => ['phpgdo-mailer', 'phpgdo-phpmailer', 'phpgdo-mailer-symfony'],
	'Session' => ['phpgdo-session-db', 'phpgdo-session-cookie'],
];

if ($mode)
{
	foreach ($multi as $mod => $prov)
	{
		$multiprov = implode("', '", $prov);
		printf("'%s' => [%s],\n", $mod, "'{$multiprov}'");
	}
}

Filewalker::traverse('GDO', null, null,
	function ($entry, $fullpath)
	{
		if (is_dir('GDO/' . $entry . '/.git'))
		{
			global $mode, $multi;
			$c = file_get_contents('GDO/' . $entry . '/.git/config');
			$c = Regex::firstMatch('#/gizmore/([-_a-z0-9]+)#miD', $c);
			if ((!str_starts_with($entry, 'phpgdo-')) &&
				(!isset($multi[$entry])))
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

return 0;
