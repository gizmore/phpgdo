<?php
namespace GDO\Admin\Test;

use GDO\Admin\GDT_ModuleVersionFS;
use GDO\Core\GDO_Module;
use GDO\Core\Module_Core;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

final class VirtualFieldTest extends TestCase
{

	public function testModuleVersionFS()
	{
		$mod = Module_Core::instance();
		$gdt = GDT_ModuleVersionFS::make()->gdo($mod);
		$html = $gdt->displayVar($gdt->getVar());
		assertEquals($mod->version, $html, 'Test GDT_ModuleVersionFS for testing GDT_Virtual implementation.');
	}

	public function testCustomField()
	{
		$mod = GDO_Module::blank(['foo' => 'bar']);
		assertFalse($mod->hasVar('foo'), 'You cannot assign GDT_Virtual fields via blank.');
		assertTrue($mod->hasColumn('module_name'), 'GDT_Virtual has is valid GDO/GDT fields.');
	}

}
