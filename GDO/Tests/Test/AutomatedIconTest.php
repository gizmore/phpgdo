<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\UI\WithIcon;
use GDO\UI\GDT_IconUTF8;
use GDO\Core\GDO;
use GDO\Core\Method;

/**
 * Test if all icons exist.
 * 
 * @author gizmore
 */
final class AutomatedIconTest extends TestCase
{
	
	public function testAllGDT() : void
	{
		foreach (get_declared_classes() as $classname)
		{
			# All GDT WithIcon
			$this->tryClassname($classname);

			if (is_subclass_of($classname, GDO::class))
			{
				if ($table = call_user_func([$classname, 'table']))
				{
					foreach ($table->gdoColumnCache() as $gdt)
					{
						$this->tryClassname(get_class($gdt));
					}
				}
			}
			
			if (is_subclass_of($classname, Method::class))
			{
				if ($method = call_user_func([$classname, 'make']))
				{
// 					if ($method->isTrivial())
					{
						foreach ($method->gdoParameterCache() as $gdt)
						{
							$this->tryClassname(get_class($gdt));
						}
					}
				}
			}
		}
	}
	
	private function tryClassname(string $classname)
	{
		$gdt = call_user_func([$classname, 'make']);
		if ($gdt->isTestable())
		{
			if ($this->class_uses_trait($classname, WithIcon::class))
			{
				if ($icon = $gdt->icon)
				{
					assert(@GDT_IconUTF8::$MAP[$icon], 'Test if icon exists for ' . $classname);
				}
			}
		}
	}
	
	private function class_uses_trait(string $classname, string $traitname) : bool
	{
		return in_array($traitname, class_uses($classname), true);
	}
	
}
