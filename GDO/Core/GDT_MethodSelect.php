<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\CLI\Method\Ekko;
use GDO\CLI\Method\Help;

/**
 * Select a method.
 * Optional permission validation.
 *
 * @version 7.0.3
 * @since 7.0.1
 * @author gizmore
 */
final class GDT_MethodSelect extends GDT_Select
{

	#################
	### Permitted ###
	#################
	public bool $onlyPermitted = false;

	public function onlyPermitted(bool $onlyPermitted = true): self
	{
		$this->onlyPermitted = $onlyPermitted;
		return $this;
	}

	###############
	### Choices ###
	###############
	protected function getChoices(): array
	{
		$choices = [];
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			foreach ($module->getMethods($this->onlyPermitted) as $method)
			{
				$choices[$method->getCLITrigger()] = $method;
			}
		}
		return $choices;
	}

	public function gdoExampleVars(): ?string
	{
		$ekko = Ekko::make()->getCLITrigger();
		$help = Help::make()->getCLITrigger();
		return "{$ekko}|{$help}|...";
	}

}
