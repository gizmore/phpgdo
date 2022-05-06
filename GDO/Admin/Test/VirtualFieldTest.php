<?php
namespace GDO\Admin\Test;

use GDO\Tests\TestCase;
use GDO\Core\GDO_Module;
use function PHPUnit\Framework\assertFalse;
use GDO\Admin\GDT_ModuleVersionFS;
use function PHPUnit\Framework\assertEquals;
use GDO\Core\Module_Core;
use function PHPUnit\Framework\assertTrue;

final class VirtualFieldTest extends TestCase
{
    public function testModuleVersionFS()
    {
        $mod = Module_Core::instance();
        $gdt = GDT_ModuleVersionFS::make()->gdo($mod);
        $html = $gdt->displayVar($gdt->getVar());
        assertEquals("6.11", $html);
    }
    
    public function testCustomField()
    {
        $mod = GDO_Module::blank(['foo' => 'bar']);
        assertFalse($mod->hasVar('foo'), 'You cannot assign custom fields via blank.');
        assertTrue($mod->hasColumn('module_name'), 'A GDO has valid GDT fields.');
    }
    
}
