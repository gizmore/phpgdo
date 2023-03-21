<?php
namespace GDO\Core;

/**
 * Select a method.
 * Optional permission validation.
 *
 * @version 7.0.2
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
	public function getChoices(): array
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

}
