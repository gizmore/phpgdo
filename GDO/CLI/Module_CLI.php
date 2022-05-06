<?php
namespace GDO\CLI;

use GDO\Core\GDO_Module;
use GDO\Core\Method;

/**
 * CLI Specific code.
 * @TODO Move CLI utils into this folder.
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.4
 */
final class Module_CLI extends GDO_Module
{
    public function onLoadLanguage() : void
    {
        $this->loadLanguage('lang/cli');
    }
    
    public function onInitCLI() : void
    {
    	Method::addCLIAlias('echo', 'GDO\\CLI\\Method\\Ekko');
    }

}
