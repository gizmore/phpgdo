<?php
namespace GDO\Tests\Test;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Tests\TestCase;
use GDO\CLI\CLI;
use GDO\Util\Permutations;
use function PHPUnit\Framework\assertEquals;
use GDO\Core\Debug;
use GDO\Core\ModuleLoader;
use GDO\Date\GDT_DateDisplay;
use GDO\Language\GDT_LangSwitch;

/**
 * Test all rendering methods on all GDO + GDT.
 * Try blank + pluggend and unplugged states.
 * Also do a performance test.
 * This is merely to detect crashes.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
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
		$this->message(CLI::bold("Starting the automated rendering test!"));
		
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
				/** @var $gdt \GDO\Core\GDT **/
				$k = new \ReflectionClass($klass);
				if ($k->isAbstract())
				{
					$this->fieldsAbstract++;
					continue;
				}
				$gdt = call_user_func([
					$klass,
					'make'
				], 'testfield');
				if (!$gdt->isTestable())
				{
					$this->fieldsNonTestable++;
					continue;
				}
				try
				{
					if (in_array('GDO\\Core\\GDO', $parents, true))
					{
						$this->fieldsGDOTested++;
						$this->fieldTestGDO($gdt);
					}
					else
					{
						$this->fieldsGDTTested++;
						$this->fieldTestGDT($gdt);
					}
					$this->message("%4d.) %s: %s",
						$this->fieldsTested,
						CLI::bold(CLI::green("SUCCESS")),
						get_class($gdt));
					$this->fieldsSuccess++;
				}
				catch (\Throwable $t)
				{
					$this->error("%4d.) %s: %s - %s",
						$this->fieldsTested,
						CLI::bold(CLI::red("FAILURE")),
						get_class($t),
						$t->getMessage());
					$this->error(Debug::backtraceException($t, false, $t->getMessage()));
					$this->fieldsFailed++;
				}
			}
		}
		$this->reportStatistics();
		assertEquals(0, $this->fieldsFailed);
	}
	
	private function reportStatistics()
	{
		$this->message(CLI::bold("DONE!"));
		$this->message('Tested %d fields (%d GDO / %d GDT)',
			$this->fieldsTested, $this->fieldsGDOTested, $this->fieldsGDTTested);
		$this->message('%s have succeeded. %s were abstract. %s. %s ',
			$this->fieldsSuccess, $this->fieldsAbstract, CLI::bold("{$this->fieldsFailed} failed"), CLI::bold("{$this->fieldsNonTestable} were not testable."));
	}
	
	###############
	### Private ###
	###############
	/**
	 * @var string[string][]
	 */
	private array $plugVariants;
	
	private function addPlugVars(string $name, array $plugs)
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
	
	private function fieldTestGDT(GDT $gdt) : bool
	{
// 		$this->renderAll($gdt); # unplugged

// 		if ($gdt instanceof GDT_LangSwitch)
// 		{
// 			xdebug_break();
// 		}

		$name = $gdt->getName();
		$name = $name ? $name : "testfield";
		$this->plugVariants = [];
		$plugs = $gdt->plugVars();
		$this->addPlugVars($name, $plugs);
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
	
	private function fieldTestGDO(GDO $gdo) : bool
	{
// 		$this->renderAllUnplugged($gdo);
		foreach ($gdo->gdoColumnsCache() as $gdt)
		{
			if ($name = $gdt->getName())
			{
				$gdt->gdo($gdo);
				$this->addPlugVars($name, $gdt->plugVars());
			}
		}
		$permutations = new Permutations($this->plugVariants);
		foreach ($permutations->generate() as $inputs)
		{
			$new = $gdo->table()->cache->getNewDummy();
			foreach ($inputs as $key => $var)
			{
				$new->setVar($key, $var);
			}
			$this->renderAll($new); # plugged with a permutation
		}
		return true;
	}

	private function renderAllUnplugged(GDT $gdt) : bool
	{
		return $this->renderAll($gdt); # unplugged
	}
	
	private function renderAll(GDT $gdt) : bool
	{
// 		if ($gdt instanceof GDT_LangSwitch)
// 		{
// 			xdebug_break();
// 		}
		# various output/rendering formats
		$gdt->renderNIL();
		$gdt->renderBinary();
		$gdt->renderCLI();
		$gdt->renderPDF();
		$gdt->renderXML();
		$gdt->renderJSON();
		$gdt->renderGTK();
		$gdt->renderPage();
		# html rendering
		$gdt->renderHTML();
		$gdt->renderOption();
		$gdt->renderList();
		$gdt->renderForm();
		$gdt->renderCard();
		# html table rendering
		$gdt->renderHTML();
// 		$gdt->renderHeader();
// 		$gdt->renderFilter('');
		return true;
	}

}
