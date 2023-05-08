<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * A module select.
 *
 * Features installed and uninstalled choices.
 * Loads modules via module loader.
 * Plugs vars for auto tests is module UI first, so nothing get's horribly hurt?
 *
 * @version 7.0.3
 * @since 6.2.0
 *
 * @author gizmore
 * @see GDO_Module
 */
final class GDT_Module extends GDT_ObjectSelect
{

	###########
	### GDT ###
	###########
	public bool $installed = true;

	public bool $uninstalled = false;

	# ###################
	# ## Un/Installed ###
	# ###################

	protected function __construct()
	{
		parent::__construct();
		$this->table(GDO_Module::table());
	}

	public function getDefaultName(): ?string
	{
		return 'module';
	}

	/**
	 * @throws GDO_Exception
	 * @throws GDO_Exception
	 */
	protected function getChoices(): array
	{
		$choices = [];
		$modules = ModuleLoader::instance()->loadModules(
			$this->installed, $this->uninstalled);
		foreach ($modules as $module)
		{
			if (
				(($module->isInstalled()) && $this->installed) ||
				((!$module->isInstalled()) && $this->uninstalled)
			)
			{
				$choices[$module->getName()] = $module;
			}
		}
		return $choices;
	}

	public function getValueSingle(?string $moduleName): ?GDO_Module
	{
		if ($moduleName)
		{
			if ($module = ModuleLoader::instance()->getModule($moduleName, false))
			{
				return $module;
			}
			return $this->toClosestChoiceValue($moduleName);
		}
		return  null;
	}

	# ##############
	# ## Choices ###
	# ##############

	public function getValueMulti($var): array
	{
		$loader = ModuleLoader::instance();
		$back = [];
		foreach (@json_decode($var) as $id)
		{
			if ($object = $loader->getModuleByID($id))
			{
				$back[$id] = $object;
			}
		}
		return $back;
	}

	# ################
	# ## Var/Value ###
	# ################

	public function plugVars(): array
	{
		$name = $this->getName();
		return [
			[$name => 'UI'],
			[$name => 'Table'],
		];
	}

	/**
	 * Also consider installed modules, or not when false.
	 */
	public function installed(bool $installed = true): self
	{
		$this->installed = $installed;
		return $this;
	}

	#############
	### Test ###
	#############

	/**
	 * Also consider / not consider uninstalled modules.
	 */
	public function uninstalled(bool $uninstalled = true): self
	{
		$this->uninstalled = $uninstalled;
		return $this;
	}

}
