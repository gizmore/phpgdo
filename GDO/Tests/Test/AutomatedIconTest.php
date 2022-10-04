<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\UI\WithIcon;
use GDO\UI\GDT_IconUTF8;

/**
 * Test if all default icons exist.
 * 
 * @author gizmore
 */
final class AutomatedIconTest extends TestCase
{
	
	public function testAllGDT() : void
	{
		foreach (get_declared_classes() as $classname)
		{
			if ($this->class_uses_trait($classname, WithIcon::class))
			{
				$this->tryClassname($classname);
			}
		}
	}
	
	private function class_uses_trait(string $classname, string $traitname) : bool
	{
		return in_array($traitname, class_uses($classname), true);
	}
	
	private function tryClassname(string $classname)
	{
		$gdt = call_user_func([$classname, 'make']);
		if ($gdt->isTestable())
		{
			if ($icon = $gdt->icon)
			{
				assert(@GDT_IconUTF8::$MAP[$icon], 'Test if icon exists for ' . $classname);
			}
		}
	}
	
}
