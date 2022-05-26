<?php
namespace GDO\UI\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Button;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Form\GDT_Form;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use GDO\UI\GDT_Label;
use GDO\Core\GDT;
use GDO\UI\GDT_Message;
use GDO\UI\GDT_Page;
use function PHPUnit\Framework\assertEquals;

final class UITest extends TestCase
{
	public function testMessageRendering()
	{
		$string = '<p><a>Test</a></p>';
		$message = GDT_Message::make('msg')->var($string);
		$html = $message->renderHTML();
		assertEquals($string, $html, 'Test if default renderer does not mess with user input.');
	}
	
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
     * We render an empty page and check if the most bottom javascript preamble contains the GDO_REVISION as JS code.
     * If this is there, the empty page has been rendered.
     * And now i introduce generic rendering tests... all this is obsolete!
     */
    public function testHTMLPageRendering()
    {
    	$content = GDT_Page::instance()->renderHTML();
    	assertStringContainsString('window.GDO_REVISION', $content, 'Test if page rendering works.');
    }
    
}
