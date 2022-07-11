<?php
namespace GDO\Admin\Test;

use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\Admin\Method\Configure;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Admin\Method\Modules;
use GDO\Core\GDT_Method;
use GDO\Core\GDT;
use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;

/**
 * Test method form for module admin configuration.
 * Test if all modules can be configured.
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
    
    public function testAllEnabledModulesToConfigure()
    {
    	$this->messageBold("Testing all enabled modules to configure.");
    	foreach (ModuleLoader::instance()->getEnabledModules() as $module)
    	{
    		$this->configureTest($module);
    	}
    }
    
    private function configureTest(GDO_Module $module)
    {
    	# Get
    	$inputs = ['module' => $module->getName()];
    	$method = GDT_Method::make()->method(Configure::make())->runAs()->inputs($inputs);
    	$result = $method->execute();
    	$html = $result->renderMode(GDT::RENDER_HTML);
    	assertStringContainsString('</form>', $html, "Test if {$module->getName()} can be configured correctly.");
    	
    	# Save
    	$inputs = ['module' => $module->getName(), 'submit' => 'submit'];
    	$method = GDT_Method::make()->method(Configure::make())->runAs()->inputs($inputs);
    	$result = $method->execute();
    	$html = $result->renderMode(GDT::RENDER_HTML);
    	$this->assert200("Test if {$module->getName()} can save it's configuration.");
    }
    
}
