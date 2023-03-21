<?php
namespace GDO\UI\Method;

use GDO\Core\GDT_Method;
use GDO\Core\ModuleLoader;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Tests\Fuzzer;
use GDO\Util\Random;

/**
 * Execute a random fuzzed command. This is dangerous!
 *
 * Greetings to tehron and spaceone!
 *
 * @since 7.0.1
 * @author gizmore
 */
final class Russlette extends MethodForm
{

	public function isTrivial(): bool
	{
		return false;
	}

	public function createForm(GDT_Form $form): void
	{
		$form->actions()->addFields(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form)
	{
		return $this->doRandStuff();
	}

	/**
	 * Gather all methods in all modules that are trivial/testable.
	 */
	public function doRandStuff()
	{
		$methods = $this->gatherAllMethods();
		$me = Random::arrayItem($methods);
		$fuzzer = new Fuzzer();
		$permutations = $fuzzer->getPermutations($me);
		$parameters = Random::arrayItem($permutations);
		$method = GDT_Method::make()->method($me)->inputs($parameters);
		return $method->execute();
	}

	private function gatherAllMethods(): array
	{
		$back = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$back = array_merge($back, $module->getMethods(true));
		}
		return $back;
	}

}
