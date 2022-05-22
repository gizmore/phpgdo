<?php
namespace GDO\Tests\Test;

use GDO\Core\GDT_Response;
use GDO\Tests\TestCase;
use GDO\UI\GDT_Paragraph;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\UI\GDT_Container;
use function PHPUnit\Framework\assertEquals;
use GDO\Core\Application;
use GDO\Core\GDT;

/**
 * Some very basic rendering tests.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.4
 */
final class ResponseTest extends TestCase
{
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
        $c->addField($p3);
        $r2->addField($c);
        $r1->addField($r2);
        
        $html = $r1->renderMode(GDT::RENDER_FORM);
        
        assertStringContainsString('par1', $html);
        assertStringContainsString('par2', $html);
        assertStringContainsString('par3', $html);
    }
    
    public function testAddingNullResponse()
    {
        $r1 = GDT_Response::make();
        $p1 = GDT_Paragraph::make()->textRaw('par1');
        $r1->addField($p1);
        assertEquals(200, Application::$RESPONSE_CODE);
    }

}
