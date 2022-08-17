<?php
namespace GDO\Tests\Test;

use GDO\Core\Module_Core;
use GDO\Tests\TestCase;
use GDO\Core\ModuleLoader;

/**
 * Very simple module testing.
 * 
 * @author gizmore
 */
final class ModuleTest extends TestCase
{
    public function testAlreadyLoaded()
    {
        # Modules are not cached. But this should be a unique identity as well.
        $loader = ModuleLoader::instance();
        $mod1 = Module_Core::instance();
        $mod2 = $loader->loadModuleFS('Core');
        $this->assertTrue($mod1 === $mod2, 'Test if single identity cache works for twice loaded modules');
    }
    
}
