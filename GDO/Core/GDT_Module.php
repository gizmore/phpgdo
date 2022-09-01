<?php
namespace GDO\Core;

/**
 * A module select.
 * 
 * Features installed and uninstalled choices.
 * Loads modules via module loader.
 * Plugs vars for auto tests is module UI first, so nothing get's horribly hurt?
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 *       
 * @see GDO_Module
 */
final class GDT_Module extends GDT_ObjectSelect
{
	###########
	### GDT ###
	###########
	public function getDefaultName(): ?string
	{
		return 'module';
	}

	protected function __construct()
	{
		parent::__construct();
		$this->table(GDO_Module::table());
	}

	# ###################
	# ## Un/Installed ###
	# ###################
	public bool $installed = true;
	public bool $uninstalled = false;

	/**
	 * Also consider installed modules, or not when false.
	 */
	public function installed(bool $installed = true) : self
	{
		$this->installed = $installed;
		return $this;
	}

	/**
	 * Also consider / not consider uninstalled modules.
	 */
	public function uninstalled(bool $uninstalled = true) : self
	{
		$this->uninstalled = $uninstalled;
		return $this;
	}

	# ##############
	# ## Choices ###
	# ##############
	public function getChoices()
	{
		$choices = [];

		$modules = ModuleLoader::instance()->loadModules(
			$this->installed, $this->uninstalled);

		foreach ($modules as $module)
		{
			if ((($module->isInstalled()) && $this->installed) ||
				((!$module->isInstalled()) && $this->uninstalled))
			{
				$choices[$module->getLowerName()] = $module->renderName();
			}
		}
		return $choices;
	}

	# ################
	# ## Var/Value ###
	# ################
	public function getValueSingle(string $moduleName): ?GDO_Module
	{
		return ModuleLoader::instance()->getModule($moduleName,
			true, false);
	}

	public function getValueMulti(string $var): array
	{
		$loader = ModuleLoader::instance();
		$back = [];
		foreach (json_decode($var) as $id)
		{
			if ($object = $loader->getModule($id))
			{
				$back[$id] = $object;
			}
		}
		return $back;
	}

	#############
	### Tests ###
	#############
	public function plugVars(): array
	{
		$name = $this->getName();
		return [
			[$name => 'UI'],
			[$name => 'Admin'],
		];
	}
	
}
