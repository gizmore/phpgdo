<?php
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\GDT_Version;
use GDO\Core\Module_Core;
use GDO\UI\GDT_Box;

/**
 * Print GDO and PHP version number.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class Version extends Method
{
	public function execute(): GDT
	{
		return GDT_Box::makeWith(
			GDT_Version::make('php_version')->var(PHP_VERSION),
			GDT_Version::make('gdo_version')->var(Module_Core::GDO_REVISION)
		)->vertical();
	}

}

