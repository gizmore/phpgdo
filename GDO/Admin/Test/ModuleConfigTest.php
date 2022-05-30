<?php
namespace GDO\Admin\Test;

use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\Admin\Method\Configure;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Admin\Method\Modules;
use GDO\Core\GDT_Method;
use GDO\Core\GDT;

/**
 * Test method form for module admin configuration.
 * 
 * @author gizmore
 */
final class ModuleConfigTest extends TestCase
{
    public function testModuleOverview()
    {
        $method = Modules::make();
        $checky = GDT_MethodTest::make()->method($method);
        $result = $checky->execute();
        $html = $result->render();
        $this->assert200("Check Admin::Modules for errors");
        assertStringContainsString(', Core', $html, 'Test if Module table can be rendered in HTML.');
    }
    
    public function testConfigure()
    {
        $inputs = ['module' => 'Table'];
        $method = GDT_Method::make()->method(Configure::make())->runAs()->inputs($inputs);
        $result = $method->execute();
        $html = $result->renderMode(GDT::RENDER_HTML);
        assertStringContainsString('"20"', $html, 'Test if configured values are prefilled correctly.');
    }
    
}
