<?php
namespace GDO\CLI;

use GDO\Core\GDO_Module;

/**
 * CLI Specific code.
 *
 * @version 7.0.1
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

//    public function onModuleInitCLI() : void
//    {
//    	Method::addCLIAlias('echo', Ekko::class);
//    }

}
