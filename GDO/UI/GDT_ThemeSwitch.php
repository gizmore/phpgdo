<?php
namespace GDO\UI;

use GDO\Core\GDT_Select;

final class GDT_ThemeSwitch extends GDT_Select
{
    public function initChoices()
    {
        if (!$this->choices)
        {
            $this->choices = $this->generateChoices();
        }
        return $this;
    }
    
    private function  generateChoices()
    {
    }

}

