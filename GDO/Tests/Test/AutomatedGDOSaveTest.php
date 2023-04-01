<?php
declare(strict_types=1);
namespace GDO\Tests\Test;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDO_Exception;
use GDO\Tests\TestCase;
use GDO\UI\Color;
use GDO\UI\TextStyle;
use GDO\Util\Permutations;
use ReflectionClass;
use Throwable;

/**
 * Try to save all GDO.
 * Once with blank. (may fail!)
 * Once with plugged. (should be a success)
 *
 * @version 7.0.3
 * @author gizmore
 */
final class AutomatedGDOSaveTest extends TestCase
{

	private int $gdoTested = 0;
	private int $gdoFailure = 0;
	private int $gdoSuccess = 0;
	private int $gdoAbstract = 0;
	private int $gdoNonTestable = 0;

	/**
	 * @throws \ReflectionException
	 */
	public function testGDOSave()
	{
		if (function_exists('t'))
		{
			return;
		}

		$this->message(CLI::bold('Starting the automated gdo save test!'));

		foreach (get_declared_classes() as $klass)
		{
			$parents = class_parents($klass);
			if (in_array('GDO\\Core\\GDO', $parents, true))
			{
				$this->gdoTested++;
				/** @var GDO $gdo * */
				$k = new ReflectionClass($klass);
				if ($k->isAbstract())
				{
					$this->gdoAbstract++;
					continue;
				}
				$gdo = call_user_func([
					$klass,
					'make',
				], 'testfield');
				if ($gdo->gdoAbstract())
				{
					$this->gdoAbstract++;
					continue;
				}

				if ((!$gdo->isTestable()) || ($gdo->gdoDTO()))
				{
					$this->gdoNonTestable++;
					continue;
				}

				$this->trySafeTestGDO($gdo);
			}
		}
	}

	###############
	### Private ###
	###############

	private function trySafeTestGDO(GDO $gdo): void
	{
		try
		{
			$success = $this->saveTestGDO($gdo);
			$this->assert200("Test if {$gdo->gdoClassName()} can be saved.");
			if (!$success)
			{
				$errors = '';
				foreach ($gdo->gdoColumnsCache() as $gdt)
				{
					if ($gdt->hasError())
					{
						$errors .= $gdt->getName() . ':' . $gdt->renderError() . "\n";
					}
				}
				throw new GDO_Exception('Cannot save blank plugged GDO: ' . get_class($gdo) . '. Reason: ' . $errors);
			}
			$this->message('%4d.) %s: %s',
				$this->gdoTested,
				CLI::bold(CLI::green('SUCCESS')),
				get_class($gdo));
			$this->gdoSuccess++;
		}
		catch (Throwable $t)
		{
			$this->error('%4d.) %s: %s - %s',
				$this->gdoTested,
				CLI::bold(CLI::red('FAILURE')),
				get_class($t),
				$t->getMessage());
			$this->error(Debug::backtraceException($t, false, $t->getMessage()));
			$this->gdoFailure++;
			Application::$INSTANCE->reset();
		}
		finally
		{
			CLI::flushTopResponse();
		}
	}

	private function saveTestGDO(GDO $gdo): bool
	{
		$success = true;
		$this->plugVariants = [];
		foreach ($gdo->gdoColumnsCache() as $gdt)
		{
// 			$gdt->inputs(null); # clear input
			$this->addPlugVars($gdt->plugVars());
		}

// 		if ($gdo instanceof \GDO\Mettwitze\GDO_MettwitzComments)
// 		{
// 			xdebug_break();
// 		}

		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $inputs)
		{
			$success = false;
			try
			{
// 				if ($gdo instanceof GDO_IPCountry)
// 				{
// 					xdebug_break();
// 				}

				$new = $gdo->tbl()->cache->getNewDummy();
				$new->setVars($inputs);
				if ($new->isValid())
				{
					$new->softReplace();
					# might ruin custom test chains :(
// 					$new->delete();
					$success = true;
					break;
				}
				else
				{
					foreach ($gdo->gdoColumnsCache() as $gdt)
					{
						if ($gdt->hasError())
						{
							$this->error('%s: %s could not save - %s %s',
								Color::red('WARNING'),
								TextStyle::bold(get_class($gdo)),
								$gdt->getName(),
								TextStyle::italic($gdt->renderError()),
							);
						}
					}
				}
			}
			catch (Throwable)
			{
				$this->message('Warning: Failed a null GDO save test for: %s', $gdo->gdoClassName());
			}
		}

		if (!$success)
		{
			$this->error('%s: %s',
				Color::red('Cannot save blank GDO'),
				TextStyle::bold($gdo->gdoClassName()));
		}

		return $success;
	}

//	private function reportStatistics()
//	{
//		$this->message(CLI::bold('DONE!'));
//		$this->message('Tested %d GDO', $this->gdoTested);
//		$this->message('%s have succeeded. %s were abstract. %s. %s ',
//			$this->gdoSuccess, $this->gdoAbstract,
//			CLI::bold("{$this->gdoFailure} failed",
//				CLI::bold("{$this->gdoNonTestable} were not testable.")));
//	}

}
