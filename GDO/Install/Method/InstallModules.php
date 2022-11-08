<?php
namespace GDO\Install\Method;

use GDO\DB\Database;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\UI\GDT_Success;
use GDO\Util\Common;
use GDO\Core\GDO_Module;
use GDO\Install\Installer;
use GDO\Install\Config;
use GDO\DB\Cache;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Paragraph;

/**
 * Install selected modules.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.0
 */
final class InstallModules extends Method
{
	/**
	 * @var GDO_Module[]
	 */
	private array $modules;
	
	public function isUserRequired() : bool
	{
		return false;
	}
	
	public function getMethodTitle() : string
	{
		return t('install_title_4');
	}
	
	public function getMethodDescription() : string
	{
		return t('install_title_4');
	}
	
	public function execute()
	{
	    Cache::fileFlush();
		Database::init();
		$loader = ModuleLoader::instance();
		$loader->loadModules(true, true, true);
		$this->modules = $loader->getInstallableModules();
		
		if ($this->hasInput('btn_install'))
		{
			return $this->onInstall(Common::getRequestArray('module'));
		}
		
		return $this->renderModuleTable();
	}
	
	public function renderModuleTable()
	{
		$tVars = array(
			'modules' => $this->modules,
			'moduleNames' => $this->getModuleNames(),
			'coreModules' => $this->getCoreModuleNames(),
			'siteModules' => $this->getSiteModuleNames(),
			'dependencies' => $this->getModuleDependencies(),
		);
		return $this->templatePHP('page/installmodules.php', $tVars);
	}
	
	private function getModuleNames()
	{
		$mods = [];
		foreach ($this->modules as $module)
		{
			$mods[] = $module->getName();
		}
		return $mods;
	}
	
	private function getCoreModules()
	{
		$mods = Installer::getDependencyModules('Core');
		return $mods;
	}
	
	private function getCoreModuleNames()
	{
		$mods = $this->getCoreModules();
		$names = array_map(function(GDO_Module $module) {
			return $module->getName();
		}, $mods);
		return $names;
	}
	
	private function getSiteModuleNames()
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
	
	private function getModuleDependencies()
	{
		foreach ($this->modules as $module)
		{
			$deps[$module->getName()] = $module->getDependencies();
		}
		return $deps;
	}
	
	public function onInstall(array $toInstall)
	{
		try
		{
			$loader = ModuleLoader::instance();
			foreach (array_keys($toInstall) as $moduleName)
			{
				$module = $loader->getModule($moduleName, true);
					if (!$module->isInstalled())
					{
 						Database::instance()->transactionBegin();
						GDT_Page::instance()->topResponse()->addField(GDT_Paragraph::make()->textRaw("Installing $moduleName..."));
						Installer::installModule($module);
						Database::instance()->transactionEnd();
					}
			}
			
			foreach ($this->modules as $module)
			{
				if ($module->isEnabled())
				{
					$module->onAfterInstall();
				}
			}
		}
		catch (\Throwable $e)
		{
			Database::instance()->transactionRollback();
			throw $e;
		}
		finally
		{
			Cache::flush();
		}
		
		return GDT_Success::make()->text('install_modules_completed', [Config::linkStep(5)]);
	}
	

	
}
