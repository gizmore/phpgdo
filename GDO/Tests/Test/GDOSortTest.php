<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\Country\GDO_Country;
use GDO\Table\Sort;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

/**
 * Try raw sorting method.
 * @author gizmore
 */
final class GDOSortTest extends TestCase
{
    public function testGDOSorting()
    {
        $table = GDO_Country::table();
        $countries = &$table->allCached();
        
        Sort::sortArray($countries, $table, ['c_iso' => 0]);
        assertEquals('ZW', key($countries), 'Test descending sorting of raw arrays.');

        Sort::sortArray($countries, $table, ['c_iso' => 1]);
        assertEquals('AD', key($countries), 'Test ascending sorting of raw arrays.');
    }
    
}
