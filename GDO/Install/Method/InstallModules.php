<?php
namespace GDO\Install\Method;

use GDO\Core\GDO_Module;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Install\Config;
use GDO\Install\Installer;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Paragraph;
use GDO\UI\GDT_Success;

/**
 * Install selected modules.
 *
 * @version 7.0.2
 * @since 3.0.0
 * @author gizmore
 */
final class InstallModules extends Method
{

	/**
	 *
	 * @var GDO_Module[]
	 */
	private array $modules;

	public function isUserRequired(): bool
	{
		return false;
	}

	public function getMethodTitle(): string
	{
		return t('install_title_4');
	}

	public function getMethodDescription(): string
	{
		return t('install_title_4');
	}

	public function execute()
	{
		Cache::fileFlush();
		Database::init();
		$loader = ModuleLoader::instance();
		$this->modules = $loader->loadModules(true, true, true);

		if ($modules = $this->getInputFor('module'))
		{
			return $this->onInstall(array_keys($modules));
		}

		return $this->renderModuleTable();
	}

	public function onInstall(array $toInstall)
	{
		try
		{
			$loader = ModuleLoader::instance();
			foreach ($toInstall as $moduleName)
			{
				$module = $loader->getModule($moduleName, true);
// 				Database::instance()->transactionBegin();
				GDT_Page::instance()->topResponse()->addField(
					GDT_Paragraph::make()->textRaw("Installing $moduleName..."));
				Installer::installModule($module);
// 				Database::instance()->transactionEnd();
			}

			foreach ($toInstall as $moduleName)
			{
				$module = $loader->getModule($moduleName, true);
				$module->onAfterInstall();
			}
		}
// 		catch (\Throwable $e)
// 		{
// // 			Database::instance()->transactionRollback();
// 			throw $e;
// 		}
		finally
		{
			Cache::flush();
		}

		return GDT_Success::make()->text('install_modules_completed', [
			Config::linkStep(5),
		]);
	}

	public function renderModuleTable()
	{
		$tVars = [
			'modules' => $this->modules,
			'moduleNames' => $this->getModuleNames(),
			'coreModules' => $this->getCoreModuleNames(),
			'siteModules' => $this->getSiteModuleNames(),
			'dependencies' => $this->getModuleDependencies(),
		];
		return $this->templatePHP('page/installmodules.php', $tVars);
	}

	private function getModuleNames(): array
	{
		$mods = [];
		foreach ($this->modules as $module)
		{
			$mods[] = $module->getName();
		}
		return $mods;
	}

	private function getCoreModuleNames(): array
	{
		return Installer::getDependencyModuleNames('Core', false, false);
	}

	private function getSiteModuleNames(): array
	{
		$mods = [];
		foreach ($this->modules as $module)
		{
			if ($module->isSiteModule())
			{
				$mods[] = $module->getName();
			}
		}
		return $mods;
	}

	private function getModuleDependencies(): array
	{
		foreach ($this->modules as $module)
		{
			$deps[$module->getName()] = $module->getDependencies();
		}
		return $deps;
	}

}
