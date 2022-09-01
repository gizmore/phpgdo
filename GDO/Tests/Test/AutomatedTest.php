<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\Core\GDO;
use GDO\Core\ModuleLoader;
use GDO\Util\Filewalker;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertLessThanOrEqual;
use function PHPUnit\Framework\assertLessThan;

/**
 * Auto coverage test.
 * Note that GDO are not treated as GDT here.
 * 
 * Includes all GDT and tries some basic make and nullable test and basic back and forth conversion.
 * Includes all GDO and tests basic blank data instanciation.
 * 
 * @TODO Includes all GDO and test plugged initial test data + insert() + replace().
 * @TODO Add real easy working support for theme cycle testing :(
 * @TODO 
 * 
 * Includes all Method and executes trivial ones.
 * Trivial methods only have parameters that can be plugged.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.0
 */
final class AutomatedTest extends TestCase
{
	private int $numMethods = 0;
	
	function testGDOandGDTsyntax()
	{
		echo "Testing GDO and GDT syntax...\n";
		ob_flush();
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			Filewalker::traverse($module->filePath(), null,
				function ($entry, $fullpath)
				{
					if ((str_starts_with($entry, 'GDT_')) ||
					(str_starts_with($entry, 'GDO_')))
					{
						require_once $fullpath;
					}
				});
		}
		assertTrue(true, 'STUB assert. We check for crash only.');
	}

	public function testEveryGDTConstructor()
	{
		$count = 0;
		echo "Testing null handling on all GDT\n";
		ob_flush();
		foreach (get_declared_classes() as $klass)
		{
			$parents = class_parents($klass);
			if (in_array('GDO\\Core\\GDT', $parents, true))
			{
				if (in_array('GDO\\Core\\GDO', $parents, true))
				{
					continue; # skip GDO
				}
				/** @var $gdt \GDO\Core\GDT **/

				$k = new \ReflectionClass($klass);
				if ($k->isAbstract())
				{
					continue;
				}
				
				$gdt = call_user_func([
					$klass,
					'make'
				], 'testfield');
				
				if ($gdt->isTestable())
				{
					$gdt->value(null);
					$value = $gdt->getValue();
					$gdt->value($value);
					$gdt->toVar($value);
					$count++;
					assertTrue(!!$gdt, "Test if {$gdt->gdoClassName()} can be created.");
					continue;
				}
			}
		}
		$this->message('%d different GDT tested.', $count);
	}

	public function testAllGDOConstructors()
	{
		$count = 0;
		$this->message('Testing blank() handling on all GDO\n');
		foreach (get_declared_classes() as $klass)
		{
			$k = new \ReflectionClass($klass);
			if ($k->isAbstract())
			{
				continue;
			}
			
			$parents = class_parents($klass);
			if (in_array(GDO::class, $parents, true))
			{
//				echo "Checking GDO $klass\n"; ob_flush();
				$table = GDO::tableFor($klass, false);
				if (($table) && (!$table->gdoAbstract()))
				{
					$count++;
					# Test GDO creation.
//					echo "Testing GDO $klass\n"; flush();
					$gdo = call_user_func([
						$klass,
						'blank'
					]);
					assertInstanceOf(GDO::class, $gdo,
						'Test if ' . $klass . ' is a GDO.');
				}
			}
		}
		
		$this->message("%d GDO tested.", $count);
		echo "{$count} GDO tested!\n";
		ob_flush();
	}

}
