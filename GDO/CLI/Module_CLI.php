<?php
namespace GDO\CLI;

use GDO\Core\GDO_Module;

/**
 * CLI Specific code.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.4
 */
final class Module_CLI extends GDO_Module
{
	public int $priority = 49;
	
    public function onLoadLanguage() : void
    {
        $this->loadLanguage('lang/cli');
    }
    
//     public function onInitCLI() : void
//     {
//     	Method::addCLIAlias('echo', Ekko::class);
//     }

}
