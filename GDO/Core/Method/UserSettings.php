<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Array;
use GDO\Core\MethodAjax;
use GDO\Core\ModuleLoader;

/**
 * Get all your settings via ajax.
 *
 * @version 7.0.3
 * @since 6.8.0
 * @author gizmore
 */
final class UserSettings extends MethodAjax
{

	public function execute(): GDT
	{
		$settings = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$moduleSettings = $module->getSettingsCache();
			$settings[$module->getName()] = [];
			foreach ($moduleSettings as $gdt)
			{
				if ($gdt->isSerializable())
				{
					$settings[$module->getName()][] = $this->gdtSetting($gdt);
				}
			}
		}
		return GDT_Array::make()->value($settings);
	}

	private function gdtSetting(GDT $gdt): array
	{
		return [
			'name' => $gdt->getName(),
			'var' => $gdt->getVar(),
			'type' => get_class($gdt),
			'config' => $gdt->configJSON(),
		];
	}

}
