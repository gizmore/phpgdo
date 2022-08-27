<?php
namespace GDO\Core\Method;

use GDO\Core\GDT_Array;
use GDO\Core\GDT_Enum;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO;
use GDO\Core\MethodAjax;

/**
 * Get all possible enum values for all entities and GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.8.0
 */
final class GetEnums extends MethodAjax
{
	public function execute()
	{
		$columns = [];
		
		# Add non abstract module tables
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			if ($classes = $module->getClasses())
			{
				foreach ($classes as $class)
				{
					if (is_subclass_of($class, 'GDO\\Core\\GDO'))
					{
						if ($table = GDO::tableFor($class))
						{
							if (!$table->gdoAbstract())
							{
								foreach ($table->gdoColumnsCache() as $name => $gdt)
								{
									if ($gdt instanceof GDT_Enum)
									{
										$columns[$table->gdoClassName().'.'.$name] = $gdt->enumValues;
									}
								}
							}
						}
					}
				}
			}
			
			if ($config = $module->getConfigCache())
			{
				foreach ($config as $gdt)
				{
					if ($gdt instanceof GDT_Enum)
					{
						$columns[$module->getName().'.config.'.$gdt->name] = $gdt->enumValues;
					}
				}
			}
		
			if ($config = $module->getUserConfigCacheConfigs())
			{
				foreach ($config as $gdt)
				{
					if ($gdt instanceof GDT_Enum)
					{
						$columns[$module->getName().'.userconfig.'.$name] = $gdt->enumValues;
					}
				}
			}

			if ($config = $module->getUserConfigCacheSettings())
			{
				foreach ($config as $gdt)
				{
					if ($gdt instanceof GDT_Enum)
					{
						$columns[$module->getName().'.settings.'.$name] = $gdt->enumValues;
					}
				}
			}
		}
		
		return GDT_Array::make()->value($columns);
	}

}
