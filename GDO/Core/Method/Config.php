<?php
namespace GDO\Core\Method;

use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_Array;
use GDO\Core\MethodAjax;
use GDO\Core\ModuleLoader;

/**
 * API Request to get all module configs.
 * Useful for JS Apps.
 *
 * @version 7.0.1
 * @since 6.9.0
 * @author gizmore
 */
final class Config extends MethodAjax
{

	public function execute(): GDT
	{
		$json = [];
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$json[$module->getName()] = $this->getModuleConfig($module);
		}
		return GDT_Array::make()->value($json);
	}

	private function getModuleConfig(GDO_Module $module)
	{
		$json = [];
		if ($config = $module->getConfigCache())
		{
			foreach ($config as $type)
			{
				if ((!$type->isHidden()) && $type->isSerializable())
				{
					if ($name = $type->getName())
					{
						$value = $module->getConfigValue($name);
						$json[$name] = $type->toVar($value);
					}
				}
			}
		}
		return $json;
	}

}
