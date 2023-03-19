<?php
namespace GDO\CLI;

use GDO\CLI\Method\Ekko;
use GDO\Core\GDO_Module;
use GDO\Core\Method;

/**
 * CLI Specific code.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.4
 */
final class Module_CLI extends GDO_Module
{
	public int $priority = 25;
	
    public function onLoadLanguage() : void
    {
        $this->loadLanguage('lang/cli');
    }
    
//    public function onModuleInitCLI() : void
//    {
//    	Method::addCLIAlias('echo', Ekko::class);
//    }

}
