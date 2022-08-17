<?php
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\GDT_Version;
use GDO\Core\Module_Core;
use GDO\UI\GDT_Box;
use GDO\Core\GDT_String;
use GDO\UI\GDT_Card;

/**
 * Print GDO and PHP version number.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Version extends Method
{
	public function getMethodTitle() : string
	{
		return t('version');
	}
	
	public function getMethodDescription() : string
	{
		return t('info_version');
	}
	
	public function execute(): GDT
	{
		return GDT_Card::makeWith(
			GDT_Version::make('php_version')->var(PHP_VERSION)->label('php_version'),
			GDT_Version::make('gdo_version')->var(Module_Core::GDO_REVISION),
			GDT_String::make('copyright')->icon('copyright')->var('2022-2023; gizmore@wechall.net'),
		);
	}

}

