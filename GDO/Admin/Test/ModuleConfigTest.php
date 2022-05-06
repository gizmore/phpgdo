<?php
namespace GDO\Admin\Test;

use GDO\Tests\MethodTest;
use GDO\Tests\TestCase;
use GDO\Admin\Method\Configure;
use function PHPUnit\Framework\assertStringContainsString;

final class ModuleConfigTest extends TestCase
{
    public function testModuleOverview()
    {
        $method = method('Admin', 'Modules');
        $checky = MethodTest::make()->json()->method($method);
        $checky->execute();
        $this->assert200("Check Admin::Modules for errors");
    }
    
    public function testConfigure()
    {
        $gp = ['module' => 'Table'];
        $m = Configure::make();
        $r = MethodTest::make()->method($m)->getParameters($gp)->execute();
        assertStringContainsString('"20"', $r->render(),
            'Test if configure values are prefilled correctly.');
    }
    
}
