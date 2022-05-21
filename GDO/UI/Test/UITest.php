<?php
namespace GDO\UI\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Button;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Form\GDT_Form;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use GDO\UI\GDT_Label;
use GDO\Core\GDT;
use GDO\UI\GDT_Page;

final class UITest extends TestCase
{
	public function testSimpleLabel()
	{
		$label = GDT_Label::make()->labelRaw('teyst');
		$result = $label->renderMode(GDT::RENDER_CELL);
		assertStringContainsString("teyst", $result, 'Test if basic rendering works.');
		assertStringContainsString("<label", $result, 'Test if basic html rendering works.');
	}
	
    public function testButtons()
    {
        $btn = GDT_Button::make()->href(hrefDefault());
        $html = $btn->renderMode(GDT::RENDER_CELL);
        assertStringContainsStringIgnoringCase(GDO_MODULE, $html, "Test if Button renders without name.");
        
        $form = GDT_Form::make();
        $form->addField($btn);
        $html = $form->renderMode(GDT::RENDER_HTML);
        assertStringContainsString('gdt-button', $html, "Test if Button renders without name inside forms.");
    }

    /**
     * This method is a fine example of the GDOv7 philosophy.
     */
    public function testHTMLPageRendering()
    {
    	$content = GDT_Page::instance()->renderHTML();
    	assertStringContainsString('window.GDO_REVISION', $content, 'Test if page rendering might work.');
    }
    
}
