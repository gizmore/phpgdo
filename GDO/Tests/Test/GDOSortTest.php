<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\Table\Sort;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;
use GDO\Language\GDO_Language;

/**
 * Test raw sorting method on the language table.
 * 
 * @author gizmore
 */
final class GDOSortTest extends TestCase
{
    public function testGDOSorting()
    {
        $table = GDO_Language::table();
        $langs = &$table->allCached();
        
        Sort::sortArray($langs, $table, ['lang_iso' => false]);
        assertEquals('zh', key($langs), 'Test descending sorting of raw arrays.');

        Sort::sortArray($langs, $table, ['lang_iso' => true]);
        assertEquals('ar', key($langs), 'Test ascending sorting of raw arrays.');
    }
    
}
