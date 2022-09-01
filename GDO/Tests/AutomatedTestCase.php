<?php
namespace GDO\Tests;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Install\Installer;
use GDO\Util\Permutations;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

/**
 * Run a test for all trivial methods.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
abstract class AutomatedTestCase extends TestCase
{

	protected $numMethods = 0;

	protected $automatedTested = 0;

	protected $automatedPassed = 0;

	protected $automatedFailed = 0;

	protected $automatedCalled = 0;

	# Num plug variants called
	protected $automatedSkippedAuto = 0;

	protected $automatedSkippedHard = 0;

	protected $automatedSkippedAbstract = 0;

	protected abstract function getTestName() : string;
	protected abstract function runMethodTest(GDT_MethodTest $mt) : void;
	
	protected function automatedMethods()
	{
		$this->message("This Test is testing all trivial methods automagically...");

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
					CLI::flushTopResponse();
				}
				else
				{
					$this->automatedSkippedAuto++;
				}
			} # is Method
		} # foreach classes

		$this->message(CLI::bold("Done with automated method test {$this->getTestName()}."));
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
		try
		{
			Application::$INSTANCE->reset();
			$n = $this->automatedTested;
			$this->automatedCalled++;
			$mt = GDT_MethodTest::make()->inputs($plugVars);
			$mt->runAs($method->plugUser());
			$mt->method($method);

			$this->runMethodTest($mt);

			$this->automatedPassed++;
			$this->message('%4d.) %s: %s (%s)', $n, CLI::bold(CLI::green('SUCCESS')), $this->boldmome($mt->method),
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

// 	private array $plugVariants;

// 	private function addPlugVariants(string $name, array $plugs)
// 	{
// 		if ( !isset($this->plugVariants[$name]))
// 		{
// 			$this->plugVariants[$name] = [];
// 		}
// 		foreach ($plugs as $plug)
// 		{
// 			if ( !in_array($plug, $this->plugVariants[$name], true))
// 			{
// 				$this->plugVariants[$name][] = $plug;
// 			}
// 		}
// 	}

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
		$trivial = true;
		$this->plugVariants = [];
		$pluggedViaMethod = false;
		
// 		foreach ($method->plugVars() as $name => $plug)
		if ($plugs = $method->plugVars())
		{
			$this->addPlugVars($plugs);
			$pluggedViaMethod = true;
		}
		
		if (!$pluggedViaMethod)
		{
			# Plug via GDTs
			$fields = $method->gdoParameters();
			foreach ($fields as $gdt)
			{
// 				if ($name = $gdt->getName())
// 				{
// 					if ($plugs = $gdt->plugVars())
// 					{
						$this->addPlugVars($gdt->plugVars());
// 					}
// 				}
			}
			
			$fields = $method->inputs($this->firstPlugPermutation())->gdoParameterCache();
			foreach ($fields as $gdt)
			{
// 				if ($name = $gdt->getName())
// 				{
// 					if ($plugs = $gdt->plugVars())
// 					{
						$this->addPlugVars($gdt->plugVars());
// 					}
// 				}
			}
		}

// 		$fields = $method->gdoParameterCache();
// 		if ( !$this->isPluggableParameters($method, $fields))
// 		{
// 			$trivial = false;
// 		}
		$fields = $method->inputs($this->firstPlugPermutation())->gdoParameterCache();
		if ( !$this->isPluggableParameters($method, $fields))
		{
			$trivial = false;
		}
		if ( !$trivial)
		{
			$this->automatedSkippedAuto++;
		}
		return $trivial;
	}

	private function isPluggableParameters(Method $method, array $fields): bool
	{
		$trivial = true;
		foreach ($fields as $gdt)
		{
			# Ouch looks not trivial
			if (($gdt->isRequired()) && ($gdt->getValue() === null))
			{
				$trivial = false;
			}
			# But maybe now
			if ( !$trivial)
			{
				if ($plugs = $gdt->plugVars())
				{
					$this->addPlugVars($plugs);
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
