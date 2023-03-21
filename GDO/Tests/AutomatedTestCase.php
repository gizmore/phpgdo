<?php
namespace GDO\Tests;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Install\Installer;
use GDO\UI\Color;
use GDO\UI\TextStyle;
use GDO\Util\Permutations;
use ReflectionClass;
use Throwable;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

/**
 * Run a test for all trivial methods / GDT / GDO
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 */
abstract class AutomatedTestCase extends TestCase
{

	protected int $numGDO = 0;

	###########
	### GDO ###
	###########
	protected int $numGDO_abstract = 0;
	protected int $numGDO_skipped = 0;
	protected int $numGDO_success = 0;
	protected int $numGDO_tested = 0;
	protected int $numGDT = 0;
	protected int $numGDT_abstract = 0;
	protected int $numGDT_skipped = 0;
	protected int $numGDT_success = 0;

	###########
	### GDT ###
	###########
	protected int $numGDT_tested = 0;
	protected int $numMethods = 0;
	protected int $automatedTested = 0;
	protected int $automatedPassed = 0;
	protected int $automatedFailed = 0;
	protected int $automatedCalled = 0;
	protected int $automatedSkippedAuto = 0;
	protected int $automatedSkippedHard = 0;
	protected int $automatedSkippedAbstract = 0;
	private $i = 0;

	##############
	### Method ###
	##############

	protected function doAllGDO(): void
	{
		foreach (get_declared_classes() as $classname)
		{
			if (is_subclass_of($classname, GDO::class, true))
			{
				$this->numGDO++;
				if ($this->class_is_abstract($classname))
				{
					$this->numGDO_abstract++;
					continue;
				}
				if (!($table = call_user_func([$classname, 'table'])))
				{
					$this->numGDO_abstract++;
					continue;
				}
				if (!$table->isTestable())
				{
					$this->numGDO_skipped++;
					continue;
				}

				$this->testGDOVariants($table);
			}
		}
	}

	protected function class_is_abstract(string $classname): bool
	{
		$k = new ReflectionClass($classname);
		return $k->isAbstract();
	}

	private function testGDOVariants(GDO $table): bool
	{
		$this->numGDO_tested++;
		$this->plugGDO($table);
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $inputs)
		{
			try
			{
				foreach ($table->gdoColumnsCache() as $gdt)
				{
					$gdt->inputs($inputs);
				}
				$this->runGDOTest($table);
				$this->numGDO_success++;
				return true;
			}
			catch (Throwable $ex)
			{
				$this->error('%s: %s',
					Color::red(get_class($ex)),
					TextStyle::bold($ex->getMessage()));
				echo Debug::debugException($ex);
			}
		}
		return false;
	}

	protected function plugGDO(GDO $gdo): void
	{
		$this->plugVariants = [];
		foreach ($gdo->gdoColumnsCache() as $gdt)
		{
			$gdt->inputs(null); # clear input
			$this->addPlugVars($gdt->gdo($gdo)->plugVars());
		}
	}

	abstract protected function runGDOTest(GDO $gdo): void;

	# Num plug variants called

	protected function doAllGDT(): void
	{
		try
		{
			foreach (get_declared_classes() as $classname)
			{
				# All GDT (+GDO)
				if (is_subclass_of($classname, GDT::class))
				{
					$this->numGDT++;
					if ($this->class_is_abstract($classname))
					{
						$this->numGDT_abstract++;
						continue;
					}
				}
				else
				{
					# other
					continue;
				}

				if (is_subclass_of($classname, GDO::class))
				{
					if (
						(!($table = call_user_func([$classname, 'table'])))
						|| ($table->gdoAbstract())
					)
					{
						$this->numGDT_abstract++;
						continue;
					}
					if (!$table->isTestable())
					{
						$this->numGDT_skipped++;
						continue;
					}
					$this->numGDT_tested++;
					$this->foreachGDOsGDT($table);
					$this->assertOK('Test if we have no gdo error');
					$this->numGDT_success++;
				}

				# All GDO Columns
				elseif (is_subclass_of($classname, GDT::class))
				{
					$gdt = call_user_func([$classname, 'make'], 'testfield');
					if (!$gdt->isTestable())
					{
						$this->numGDT_skipped++;
						continue;
					}
					$this->numGDT_tested++;
					$this->runGDTTest($gdt);
					$this->assertOK('Test if we have no error');
					$this->numGDT_success++;
				}
			}
			$this->assertOK('Test if we still have no error');
		}
		catch (Throwable $ex)
		{
			echo Debug::backtraceException($ex);
			@ob_flush();
			throw $ex;
		}
	}

	private function foreachGDOsGDT(GDO $gdo): bool
	{
		$success = false;
		$this->plugGDO($gdo);
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $inputs)
		{
			try
			{
				foreach ($gdo->gdoColumnsCache() as $gdt)
				{
					$gdt->inputs($inputs);
					if ($gdt->validated())
					{
						$this->numGDT_tested++;
						$this->runGDTTest($gdt);
						$this->numGDT_success++;
						if ($gdt->hasError())
						{
							$this->error('%s: %s - %s could not runGDTTest() - %s: %s',
								Color::red('WARNING'),
								$this->getTestName(),
								TextStyle::bold(get_class($gdo)),
								$gdt->getName(),
								TextStyle::italic($gdt->renderError()),
							);
						}
						else
						{
							$success = true;
						}
					}
				}
			}
			catch (Throwable $ex)
			{
				$this->error('%s: %s',
					Color::red(get_class($ex)),
					TextStyle::bold($ex->getMessage()));
				echo Debug::debugException($ex);
			}
		}
		assert($success);
		return $success;
	}

	abstract protected function runGDTTest(GDT $gdt): void;

	abstract protected function getTestName(): string;

	protected function plugMethod(Method $method): void
	{
		$this->plugVariants = [];
		foreach ($method->gdoParameterCache() as $gdt)
		{
			$gdt->inputs(null); # clear input
			$this->addPlugVars($gdt->plugVars());
		}
	}

	protected function doAllMethods()
	{
		$this->message('This Test is testing all trivial methods automagically...');

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

		$this->_doAllMethodsB();
	}

	private function _doAllMethodsB()
	{
		foreach (get_declared_classes() as $klass)
		{
			$parents = class_parents($klass);
			if (in_array('GDO\\Core\\Method', $parents, true))
			{
				# Skip abstract
				$k = new ReflectionClass($klass);
				if ($k->isAbstract())
				{
					$this->automatedSkippedAbstract++;
					continue;
				}

				# Check others
				/** @var $method Method * */
				$method = call_user_func([
					$klass,
					'make',
				]);

				# Skip special marked
				if (!$method->isTrivial())
				{
					$this->automatedSkippedHard++;
					continue;
				}

				# Try if pluggable
				if ($this->isPluggableMethod($method))
				{
					$this->tryTrivialMethod($method);
// 					CLI::flushTopResponse();
				}
				else
				{
					$this->automatedSkippedAuto++;
				}
			} # is Method
		} # foreach classes

		$this->message(Color::green(CLI::bold("Done with automated method test {$this->getTestName()}.")));
		$this->message('Tested %s trivial methods.', TextStyle::bold($this->automatedTested));
		$this->message(CLI::bold($this->automatedFailed . ' FAILED!'));
		$this->message('%s were skipped because they were abstract.', CLI::bold($this->automatedSkippedAbstract));
		$this->message('%s were skipped because they were unpluggable.', CLI::bold($this->automatedSkippedAuto));
		$this->message('%s have been manually skipped via Method settings.', CLI::bold($this->automatedSkippedHard));
	}

	private function isPluggableMethod(Method $method)
	{
		$trivial = true;
		$this->plugVariants = [];
// 		$pluggedViaMethod = false;

		if ($plugs = $method->plugVars())
		{
			$this->addPlugVars($plugs);
			$method->inputs($plugs[0]);
// 			$pluggedViaMethod = true;
		}

// 		if (!$pluggedViaMethod)
		{
			# Plug via GDTs
			$fields = $method->gdoParameters();
			foreach ($fields as $gdt)
			{
				$this->addPlugVars($gdt->plugVars());
			}

			$fields = $method->inputs($this->firstPlugPermutation())->gdoParameterCache();
			foreach ($fields as $gdt)
			{
				$this->addPlugVars($gdt->plugVars());
			}
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

	private function firstPlugPermutation()
	{
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $p)
		{
			return $p;
		}
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
			if (!$trivial)
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

	private function tryTrivialMethod(Method $method)
	{
		$this->automatedTested++;
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $plugVars)
		{
			# This fixes old input in method from previous permutations.
			$method = call_user_func([get_class($method), 'make']);
			$this->tryTrivialMethodVariant($method, $plugVars);
		}
	}

	private function tryTrivialMethodVariant(Method $method, array $plugVars)
	{
		try
		{
// 			if ($method instanceof CreateThread)
// 			{
// 				$this->i++;
// 				echo "tryTrivialMethodVariant({$method->getCLITrigger()} $this->i)\n";
// 				@ob_flush();
// 				if ($this->i === 3)
// 				{
// 					xdebug_break();
// 				}
// 			}


			Application::$INSTANCE->reset();
			$n = $this->automatedTested;
			$this->automatedCalled++;
			$mt = GDT_MethodTest::make()->inputs($plugVars);
			$mt->runAs($method->plugUser());
			$mt->method($method);
			$method->inputs($plugVars);
			$this->runMethodTest($mt);
			$this->automatedPassed++;
			$this->message('%4d.) %s: %s (%s)', $n, CLI::bold(CLI::green('SUCCESS')), $this->boldmome($mt->method),
				json_encode($plugVars));
		}
		catch (Throwable $ex)
		{
			Logger::logException($ex);
			Debug::exception_handler($ex);
			$this->automatedFailed++;
			$this->error('%4d.) %s: %s', $n, CLI::red('FAILURE'), $this->boldmome($mt->method));
			$this->error('Error: %s', CLI::bold($ex->getMessage()));
		}
	}

	##############
	### Helper ###
	##############
// 	protected function class_uses_trait(string $classname, string $traitname) : bool
// 	{
// 		return in_array($traitname, class_uses($classname, true), true);
// 	}

	abstract protected function runMethodTest(GDT_MethodTest $mt): void;

}
