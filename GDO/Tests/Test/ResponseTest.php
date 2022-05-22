<?php
namespace GDO\Tests\Test;

use GDO\Core\GDT_Response;
use GDO\Tests\TestCase;
use GDO\UI\GDT_Paragraph;
use GDO\UI\GDT_Container;
use GDO\Core\GDT;
use function PHPUnit\Framework\assertEquals;

/**
 * Some very basic rendering tests.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.4
 */
final class ResponseTest extends TestCase
{
	/**
	 * Test nested WithFields.
	 */
    public function testRendersNestedFields()
    {
        $r1 = GDT_Response::make();
        $p1 = GDT_Paragraph::make()->textRaw('par1');
        $r1->addField($p1);
        $r2 = GDT_Response::make();
        $p2 = GDT_Paragraph::make()->textRaw('par2');
        $r2->addField($p2);
        $c = GDT_Container::make();
        $p3 = GDT_Paragraph::make()->textRaw('par3');
        # 1 -> 2 -> 3
        $c->addField($p3);
        $r2->addField($c);
        $r1->addField($r2);
        
        $html = $r1->renderMode(GDT::RENDER_FORM);
        
        assertEquals(1, substr_count($html, 'par1'), 'Test nested container rendering pass 1');
        assertEquals(1, substr_count($html, 'par2'), 'Test nested container rendering pass 2');
        assertEquals(1, substr_count($html, 'par3'), 'Test nested container rendering pass 3');
    }
    
}
