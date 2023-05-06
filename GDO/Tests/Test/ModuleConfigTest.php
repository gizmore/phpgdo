<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\Admin\Method\Configure;
use GDO\Admin\Method\Modules;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_Method;
use GDO\Core\ModuleLoader;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\UI\Color;
use GDO\UI\TextStyle;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertTrue;

/**
 * Test method form for module admin configuration.
 * Test if all modules can be configured.
 *
 * @author gizmore
 * @version 7.0.3
 */
final class ModuleConfigTest extends TestCase
{

	public function testModuleOverview()
	{
		if (module_enabled('Admin') && (\gdo_test::instance()->config))
		{
			$method = Modules::make();
			$checky = GDT_MethodTest::make()->method($method);
			$result = $checky->execute();
			$html = $result->renderCLI();
			$this->assert200('Check Admin::Modules for errors');
			assertStringContainsString(', Core', $html, 'Test if Module table can be rendered in CLI mode.');
		}
		else
		{
			assertTrue(true, 'stub');
		}
	}

	public function testAllEnabledModulesToConfigure()
	{
		if (\gdo_test::instance()->config)
		{
			if (module_enabled('Admin'))
			{
				$this->message('Testing all enabled modules to configure.');
				foreach (ModuleLoader::instance()->getEnabledModules() as $module)
				{
					$this->configureTest($module);
				}
			}
		}
		assertTrue(true, 'stub');
	}

	private function configureTest(GDO_Module $module)
	{
		# Get
		$inputs = ['module' => $module->getModuleName()];
		$method = GDT_Method::make()->method(Configure::make())->runAs()->inputs($inputs);
		$result = $method->execute();
		$html = $result->renderMode(GDT::RENDER_WEBSITE);
		assertStringContainsString('</form>', $html, "Test if {$module->getName()} can be configured correctly.");

		# Save
		$inputs = ['module' => $module->getModuleName(), 'submit' => 'submit'];
		$method = GDT_Method::make()->method(Configure::make())->runAs()->inputs($inputs);
		$result = $method->execute();

		# Check
		$errors = [];
		foreach ($module->getConfigCache() as $gdt)
		{
			if ($gdt->hasError())
			{
				$errors[] = sprintf('`%s`: %s',
					$gdt->getName(),
					TextStyle::italic($gdt->renderError()));
			}
		}
		if (count($errors))
		{
			$this->error('%s: %s cannot save config; %s',
				Color::red('Warning'),
				TextStyle::bold($module->getName()),
				TextStyle::italic(implode(' - ', $errors)),
			);
		}

		assertEmpty($errors, "Test if {$module->getName()} can save config.");
		$html = $result->renderMode(GDT::RENDER_WEBSITE);
		$this->assert200("Test if {$module->getName()} can save it's configuration.");
	}

}
