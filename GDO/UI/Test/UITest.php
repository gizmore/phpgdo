<?php
namespace GDO\UI\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Button;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Form\GDT_Form;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use GDO\UI\GDT_Label;
use GDO\Core\GDT;

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
        $html = $btn->render();
        assertStringContainsStringIgnoringCase(GDO_MODULE, $html, "Test if Button renders without name.");
        
        $form = GDT_Form::make();
        $form->addField($btn);
        $html = $form->render();
        assertStringContainsString('gdt-button', $html, "Test if Button renders without name inside forms.");
    }
    
}
