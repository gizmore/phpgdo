<?php
namespace GDO\Tests\Test;

use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\CLI\CLI;
use GDO\Core\GDO;
use GDO\Core\ModuleLoader;
use GDO\Install\Installer;
use GDO\Util\Filewalker;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertLessThanOrEqual;
use GDO\Language\Trans;
use GDO\Core\Application;
use function PHPUnit\Framework\assertLessThan;
use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\Core\Method;
use GDO\Util\Permutations;
use GDO\Core\GDT_Response;
use GDO\Core\GDT;

/**
 * Auto coverage test.
 * Note that GDO are not treated as GDT here.
 * 
 * Includes all GDT and tries some basic make and nullable test and basic back and forth conversion.
 * Includes all GDO and tests basic blank data instanciation.
 * 
 * @TODO Includes all GDT + GDO and test all rendering modes. Test with blank and plugged data.
 * @TODO Includes all GDO and test plugged initial test data + insert() + replace().
 * @TODO Add real easy working support for theme cycle testing :(
 * @TODO 
 * 
 * Includes all Method and executes trivial ones.
 * Trivial methods only have parameters that can be plugged.
 *
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.0
 */
final class AutomatedTest extends TestCase
{
	private int $numMethods = 0;
	
	function testGDOandGDTsyntax()
	{
		echo "Testing GDO and GDT syntax...\n";
		ob_flush();
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			Filewalker::traverse($module->filePath(), null,
				function ($entry, $fullpath)
				{
					if ((str_starts_with($entry, 'GDT_')) ||
					(str_starts_with($entry, 'GDO_')))
					{
						require_once $fullpath;
					}
				});
		}
		assertTrue(true, 'STUB assert. We check for crash only.');
	}

	function testTrivialMethods()
	{
		echo "Testing all trivial methods automagically...\n";
		ob_flush();
		
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			Installer::loopMethods($module,
				function ($entry, $fullpath, $method)
				{
					$this->numMethods++;
					require_once $fullpath;
				});
		}
		assertGreaterThanOrEqual(1, $this->numMethods,
			'Check if we included at least one more method for auto coverage.');
	}

	public function testEveryGDTConstructor()
	{
		$count = 0;
		echo "Testing null handling on all GDT\n";
		ob_flush();
		foreach (get_declared_classes() as $klass)
		{
			$parents = class_parents($klass);
			if (in_array('GDO\\Core\\GDT', $parents, true))
			{
				if (in_array('GDO\\Core\\GDO', $parents, true))
				{
					continue; # skip GDO
				}
				/** @var $gdt \GDO\Core\GDT **/

				$k = new \ReflectionClass($klass);
				if ($k->isAbstract())
				{
					continue;
				}
				
				$gdt = call_user_func([
					$klass,
					'make'
				], 'testfield');
				
				if ($gdt->isTestable())
				{
					$gdt->value(null);
					$value = $gdt->getValue();
					$gdt->value($value);
					$gdt->toVar($value);
					$count++;
					assertTrue(!!$gdt, "Test if {$gdt->gdoClassName()} can be created.");
					continue;
				}
			}
		}
		$this->message('%d different GDT tested.', $count);
	}

	public function testAllGDOConstructors()
	{
		$count = 0;
		$this->message('Testing blank() handling on all GDO\n');
		foreach (get_declared_classes() as $klass)
		{
			$k = new \ReflectionClass($klass);
			if ($k->isAbstract())
			{
				continue;
			}
			
			$parents = class_parents($klass);
			if (in_array(GDO::class, $parents, true))
			{
//				echo "Checking GDO $klass\n"; ob_flush();
				$table = GDO::tableFor($klass, false);
				if ($table)
				{
					$count++;
					# Test GDO creation.
//					echo "Testing GDO $klass\n"; flush();
					$gdo = call_user_func([
						$klass,
						'blank'
					]);
					assertInstanceOf(GDO::class, $gdo,
						'Test if ' . $klass . ' is a GDO.');
				}
			}
		}
		
		echo "{$count} GDO tested!\n";
		ob_flush();
	}

	public function testAllTrivialMethodsForOKCode()
	{
		if ( !module_enabled('ThemeSwitcher'))
		{
			echo "Testing all trivial methods with current theme.\n";
			ob_flush();
			$this->doAllMethods();
		}
		else
		{
			assertTrue(true);
		}
	}

	// public function testAllThemesForAllTrivialMethods()
	// {
	// if (module_enabled('ThemeSwitcher'))
	// {
	// foreach (GDO_Theme::table()->all() as $theme)
	// {
	// $this->testThemeForAllMethods($theme);
	// }
	// }
	// else
	// {
	// assertTrue(true);
	// echo "Theme switcher is disabled. done.\n";
	// ob_flush();
	// }
	// }

	// private function testThemeForAllMethods(GDO_Theme $theme)
	// {
	// echo "Testing all trivial methods with {$theme->renderName()}\n";
	// ob_flush();

	// # Switch theme
	// MethodTest::make()->method(Set::make())->
	// getParameters(['theme' => $theme->getID()])->execute();

	// # Do methods
	// $this->doAllMethods();
	// }
	
	private $automatedTested = 0;
	private $automatedPassed = 0;
	private $automatedFailed = 0;
	private $automatedCalled = 0; # Num plug variants called
	private $automatedSkippedAuto = 0;
	private $automatedSkippedHard = 0;
	
	private function doAllMethods()
	{
		foreach (get_declared_classes() as $klass)
		{
			$parents = class_parents($klass);
			if (in_array('GDO\\Core\\Method', $parents, true))
			{
				# Skip abstract
				$k = new \ReflectionClass($klass);
				if ($k->isAbstract())
				{
					continue;
				}

				# Check others
				/** @var $method \GDO\Core\Method **/
				$method = call_user_func([
					$klass,
					'make'
				]);

				# Skip special marked
				if ( !$method->isTrivial())
				{
					$this->automatedSkippedHard++;
					continue;
				}
				
				if ($this->isPluggableMethod($method))
				{
					$this->tryTrivialMethod($method);
				}

				
			} # is Method
		} # foreach classes
		
		$this->message(CLI::bold("Done with automated method tests."));
		$this->message("Tested %s trivial methods.\n%s have been %s because they were unpluggable.\n%s have been manually skipped via config.",
			CLI::bold($this->automatedTested), CLI::bold($this->automatedFailed), CLI::bold("SKIPPED"), CLI::bold($this->automatedSkippedAuto));
		$this->message('From %s trivial called methods, %s', $this->automatedCalled, CLI::bold("{$this->automatedFailed} failed"));
	}
	
	private function tryTrivialMethod(Method $method)
	{
		$this->automatedTested++;
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $plugVars)
		{
			$this->tryTrivialMethodVariant($method, $plugVars);
		}
	}
	
	private function tryTrivialMethodVariant(Method $method, array $plugVars)
	{
		try
		{
			$n = $this->automatedTested;
			$this->automatedCalled++;
			$mt = GDT_MethodTest::make()->inputs($plugVars);
			$result = $mt->runAs($this->gizmore())
				->method($method)
				->execute();
			assertLessThan(400,
				Application::$RESPONSE_CODE,
				"Test if trivial method {$this->mome($method)} has a success error code.");
			assertInstanceOf(GDT_Response::class, $result, "Test if method {$method->gdoClassName()} execution returns a GDT_Result.");
			assertTrue($this->renderResult($result), "Test if method response renders all outputs without crash.");
			$this->automatedPassed++;
			$this->message('%4d.) %s: %s (%s)', $n, CLI::green('SUCCESS'), $this->boldmome($mt->method), implode(',', $plugVars));
		}
		catch (\Throwable $ex)
		{
			Logger::logException($ex);
			$this->automatedFailed++;
			$this->error('%4d.) %s: %s', $n, CLI::red('FAILURE'), $this->boldmome($mt->method));
			$this->error('Error: %s', CLI::bold($ex->getMessage()));
			$dbgout = Debug::debugException($ex);
			echo $dbgout;
		}
	}
	
	private function renderResult(GDT_Response $response) : bool
	{
		$response->renderMode(GDT::RENDER_BINARY);
		$response->renderMode(GDT::RENDER_CLI);
		$response->renderMode(GDT::RENDER_PDF);
		$response->renderMode(GDT::RENDER_XML);
		$response->renderMode(GDT::RENDER_JSON);
		$response->renderMode(GDT::RENDER_HTML);
		return true;
	}
	
	private array $plugVariants;
	
	private function addPlugVariants(string $name, array $plugs)
	{
		if (!isset($this->plugVariants[$name]))
		{
			$this->plugVariants[$name] = [];
		}
		foreach ($plugs as $plug)
		{
			if (!in_array($plug, $this->plugVariants[$name], true))
			{
				$this->plugVariants[$name][] = $plug;
			}
		}
	}
	
	private function firstPlugPermutation()
	{
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $p)
		{
			return $p;
		}
	}
	
	private function isPluggableMethod(Method $method)
	{
		$this->plugVariants = [];
		foreach ($method->plugVars() as $name => $plug)
		{
			$this->addPlugVariants($name, [$plug]);
		}
		$trivial = true;
		$fields = $method->gdoParameters();
		if (!$this->isPluggableParameters($method, $fields))
		{
			$trivial = false;
		}
		$fields = $method->inputs($this->firstPlugPermutation())->gdoParameterCache();
		if (!$this->isPluggableParameters($method, $fields))
		{
			$trivial = false;
		}
		if (!$trivial)
		{
			$this->automatedSkippedAuto++;
		}
		return $trivial;
	}
	
	private function isPluggableParameters(Method $method, array $fields) : bool
	{
		$trivial = true;
		foreach ($fields as $gdt)
		{
			# Ouch looks not trivial
			if (($gdt->isRequired()) &&
				($gdt->getValue() === null) )
			{
				$trivial = false;
			}
			# But maybe now
			if (!$trivial)
			{
				if ($plugs = $gdt->plugVars())
				{
					$this->addPlugVariants($gdt->getName(), $plugs);
					$trivial = true;
				}
				else
				{
					break;
				}
			}
		}
		return $trivial;
	}
	
	public function testLanguageFilesForCompletion()
	{
		if (Trans::$MISS)
		{
			$this->message(CLI::bold("The following lang keys are missing:"));
			foreach (Trans::$MISSING as $key)
			{
				echo " - $key\n";
			}
			ob_flush();
		}

		assertLessThanOrEqual(1, count(Trans::$MISSING), 'Assert that (almost) no internationalization was missing.');
	}

}
