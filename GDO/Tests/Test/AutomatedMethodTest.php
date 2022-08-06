<?php
namespace GDO\Tests\Test;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Install\Installer;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\Util\Permutations;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertLessThan;
use GDO\User\Method\Profile;
use GDO\CLI\Method\Concat;

/***
 * Test all GDOv7 with plugvar fuzzing.
 * 
 * @author gizmore
 * @see Permutations
 *   
 */
final class AutomatedMethodTest extends TestCase
{
	public function testAllMethods() : void
	{
		$this->automatedMethods();
	}
	
	private $numMethods = 0;
	private $automatedTested = 0;
	private $automatedPassed = 0;
	private $automatedFailed = 0;
	private $automatedCalled = 0; # Num plug variants called
	private $automatedSkippedAuto = 0;
	private $automatedSkippedHard = 0;
	private $automatedSkippedAbstract = 0;
	
	private function automatedMethods()
	{
		$this->messageBold("Testing all trivial methods automagically...");
		
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
		
		$this->doAllMethods();
	}
	
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
					$this->automatedSkippedAbstract++;
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
				
				# Try if pluggable
				if ($this->isPluggableMethod($method))
				{
					$this->tryTrivialMethod($method);
				}
				else
				{
					$this->automatedSkippedAuto++;
				}
			} # is Method
		} # foreach classes
		
		$this->message(CLI::bold("Done with automated method tests."));
		$this->message("Tested %s trivial methods.", CLI::bold($this->automatedTested));
		$this->message(CLI::bold($this->automatedFailed . ' FAILED!'));
		$this->message("%s were skipped because they were abstract.", CLI::bold($this->automatedSkippedAbstract));
		$this->message("%s were skipped because they were unpluggable.", CLI::bold($this->automatedSkippedAuto));
		$this->message("%s have been manually skipped via Method settings.", CLI::bold($this->automatedSkippedHard));
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
// 		if ($method instanceof Concat)
// 		{
// 			xdebug_break();
// 		}
		
		try
		{
			Application::$INSTANCE->reset();
			$n = $this->automatedTested;
			$this->automatedCalled++;
			$mt = GDT_MethodTest::make()->inputs($plugVars);
			$mt->runAs($this->gizmore());
			$mt->method($method);
			$result = $mt->execute();
			assertLessThan(500,
				Application::$RESPONSE_CODE,
				"Test if trivial method {$this->mome($method)} has a success error code.");
				assertInstanceOf(GDT::class, $result, "Test if method {$method->gdoClassName()} execution returns a GDT.");
				assertTrue($this->renderResult($result), "Test if method response renders all outputs without crash.");
				$this->automatedPassed++;
				$this->message('%4d.) %s: %s (%s)',
					$n, CLI::bold(CLI::green('SUCCESS')),
					$this->boldmome($mt->method),
					json_encode($plugVars));
		}
		catch (\Throwable $ex)
		{
			Logger::logException($ex);
			Debug::exception_handler($ex);
			$this->automatedFailed++;
			$this->error('%4d.) %s: %s', $n, CLI::red('FAILURE'), $this->boldmome($mt->method));
			$this->error('Error: %s', CLI::bold($ex->getMessage()));
		}
	}
	
	private function renderResult(GDT $response) : bool
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
		foreach ($fields as $gdt)
		{
			if ($name = $gdt->getName())
			{
				if ($plugs = $gdt->plugVars())
				{
					$this->addPlugVariants($name, $plugs);
				}
			}
		}
		
		$fields = $method->inputs($this->firstPlugPermutation())->gdoParameterCache();
		foreach ($fields as $gdt)
		{
			if ($name = $gdt->getName())
			{
				if ($plugs = $gdt->plugVars())
				{
					$this->addPlugVariants($name, $plugs);
				}
			}
		}
		
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
	
}
