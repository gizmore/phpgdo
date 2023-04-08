<?php
namespace GDO\Tests\Test;

use GDO\CLI\CLI;
use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\Tests\TestCase;
use GDO\Util\Permutations;
use ReflectionClass;
use Throwable;
use function PHPUnit\Framework\assertEquals;

/**
 * Test all rendering methods on all GDO + GDT.
 * Try blank + pluggend and unplugged states.
 * Also do a performance test.
 * This is merely to detect crashes.
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
final class AutomatedRenderingTest extends TestCase
{

	private int $fieldsTested = 0;
	private int $fieldsFailed = 0;
	private int $fieldsSuccess = 0;
	private int $fieldsGDOTested = 0;
	private int $fieldsGDTTested = 0;
	private int $fieldsAbstract = 0;
	private int $fieldsNonTestable = 0;

	/**
	 * Loop them and collect statistics.
	 */
	public function testAutomatedRendering()
	{
		if (!\gdo_test::instance()->rendering)
		{
			self::assertTrue(true);
			return;
		}

		$this->message(CLI::bold('Starting the automated rendering test!'));

		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			$module->getMethods(false);
		}

		foreach (get_declared_classes() as $klass)
		{
			$parents = class_parents($klass);
			if (in_array('GDO\\Core\\GDT', $parents, true))
			{
				$this->fieldsTested++;
				/** @var GDT $gdt * */
				$k = new ReflectionClass($klass);
				if ($k->isAbstract())
				{
					$this->fieldsAbstract++;
					continue;
				}
				$gdt = call_user_func([
					$klass,
					'make',
				], 'testfield');
				if (!$gdt->isTestable())
				{
					$this->fieldsNonTestable++;
					continue;
				}
				try
				{
					$t1 = microtime(true);
					if (in_array('GDO\\Core\\GDO', $parents, true))
					{
						$this->fieldsGDOTested++;
						$this->message("Testing GDO " . get_class($gdt));
						$this->fieldTestGDO($gdt);
					}
					else
					{
						$this->fieldsGDTTested++;
						$this->message("Testing GDT " . get_class($gdt));
						$this->fieldTestGDT($gdt);
					}
					$t1 = microtime(true) - $t1;
					$this->message('%4d.) %s: %s (%.03f)',
						$this->fieldsTested,
						CLI::bold(CLI::green('SUCCESS')),
						get_class($gdt),
						$t1 / 1000.0);
					$this->fieldsSuccess++;
				}
				catch (Throwable $t)
				{
					$this->error('%4d.) %s: %s - %s',
						$this->fieldsTested,
						CLI::bold(CLI::red('FAILURE')),
						get_class($t),
						$t->getMessage());
					$this->error(Debug::backtraceException($t, false, $t->getMessage()));
					$this->fieldsFailed++;
				}

				CLI::flushTopResponse();
			}
		}
		$this->reportStatistics();
		assertEquals(0, $this->fieldsFailed);
	}

	private function fieldTestGDO(GDO $gdo): bool
	{
		if ($gdo->gdoAbstract())
		{
			return false; # true? not used
		}
// 		$this->renderAllUnplugged($gdo);
		$this->plugVariants = [];
		foreach ($gdo->gdoColumnsCache() as $gdt)
		{
// 			if ($name = $g<dt->getName())
// 			{
			$gdt->gdo($gdo);
			$this->addPlugVars($gdt->plugVars());
// 			}
		}
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $inputs)
		{
			if (!($new = $gdo->table()->select()->first()->exec()->fetchObject()))
			{
				$new = $gdo->table()->cache->getNewDummy();
			}
			foreach ($inputs as $key => $var)
			{
				$new->setVar($key, $var);
			}
			$this->renderAll($new); # plugged with a permutation
		}
		return true;
	}

	###############
	### Private ###
	###############

	private function renderAll(GDT $gdt): bool
	{
// 		if ($gdt instanceof GDT_LangSwitch)
// 		{
// 			xdebug_break();
// 		}

//		$this->message('Rendering all modes %s', $gdt->gdoClassName());

		# various output/rendering formats
		$gdt->renderBinary();
		$gdt->renderCLI();
		$gdt->renderPDF();
		$gdt->renderXML();
		$gdt->renderJSON();
		$gdt->renderGTK();
		$gdt->renderWebsite();
		# html rendering
		$gdt->renderHTML();
		$gdt->renderOption();
		$gdt->renderList();
		$gdt->renderForm();
		$gdt->renderCard();
		# html table rendering
		$gdt->renderCell();
		$gdt->renderTHead();
		$gdt->renderTFoot();
		return true;
	}

	private function fieldTestGDT(GDT $gdt): bool
	{
// 		$this->renderAll($gdt); # unplugged

// 		if ($gdt instanceof GDT_LangSwitch)
// 		{
// 			xdebug_break();
// 		}

		$name = $gdt->getName();
		$name = $name ? $name : 'testfield';
		$this->plugVariants = [];
		$plugs = $gdt->plugVars();
		$this->addPlugVars($plugs);
		$permutations = new Permutations($this->plugVariants);
		$success = true;
		foreach ($permutations->generate() as $inputs)
		{
			$gdt->inputs($inputs);
			if (!$this->renderAll($gdt)) # plugged
			{
				$success = false;
			}
		}
		return $success;
	}

	private function reportStatistics()
	{
		$this->message(CLI::bold('DONE!'));
		$this->message('Tested %d fields (%d GDO / %d GDT)',
			$this->fieldsTested, $this->fieldsGDOTested, $this->fieldsGDTTested);
		$this->message('%s have succeeded. %s were abstract. %s. %s ',
			$this->fieldsSuccess, $this->fieldsAbstract, CLI::bold("{$this->fieldsFailed} failed"), CLI::bold("{$this->fieldsNonTestable} were not testable."));
	}

	private function renderAllUnplugged(GDT $gdt): bool
	{
		return $this->renderAll($gdt); # unplugged
	}

}
