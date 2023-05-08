<?php
namespace GDO\Util;

use GDO\Core\GDO_Exception;

/**
 * APACHE 2.2 / 2.4 .htaccess utility
 *
 * @version 7.0.0
 * @author gizmore
 */
final class HTAccess
{

	public static function protectFolder($path)
	{
		$content = <<<EOF
<IfModule mod_authz_core.c>
  Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
  Deny from all
</IfModule>
EOF;
		# Create if not exist.
		if (!is_dir($path))
		{
			@mkdir($path, GDO_CHMOD, true);
		}

		if ((!is_dir($path)) || (!is_readable($path)))
		{
			throw new GDO_Exception('err_no_dir');
		}
		else
		{
			file_put_contents("$path/.htaccess", $content);
		}
	}

}
