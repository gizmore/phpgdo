<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * An HTML <pre> element.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.4
 * @see GDT_DIV
 * @see GDT_Tabs
 * @see GDT_Headline
 */
final class GDT_Pre extends GDT
{
    use WithText;
    
    public function renderHTML() : string
    {
    	return sprintf('<pre class="gdt-pre">%s</pre>', $this->renderText());
    }
    
    public function renderJSON()
    {
        return $this->renderText();
    }
    
    public function renderCLI() : string
    {
        return $this->renderText() . "\n";
    }
    
}
