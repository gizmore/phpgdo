<?php
declare(strict_types=1);
namespace GDO\CLI;

use GDO\Core\GDO_Module;

/**
 * CLI Specific code.
 *
 * @version 7.0.3
 * @since 6.10.4
 * @author gizmore
 */
final class Module_CLI extends GDO_Module
{

	public int $priority = 25;

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/cli');
	}

	public function checkSystemDependencies(): bool
	{
		if (!function_exists('readline'))
		{
			return $this->errorSystemDependency('err_php_extension', ['readline']);
		}
		return true;
	}

}
