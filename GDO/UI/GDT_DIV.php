<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Simple html DIV element.
 * @author gizmore
 */
final class GDT_DIV extends GDT
{
    use WithText;
    use WithPHPJQuery;
    
    public function renderCell() : string
    {
        return sprintf("<div %s>%s</div>",
            $this->htmlAttributes(), $this->renderText());
    }
    
    public function renderForm() : string { return $this->renderCell(); }
    
    public function renderCard() : string { return $this->renderCell(); }

}
