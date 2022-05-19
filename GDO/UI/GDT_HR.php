<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A HR element, but it uses a div to design the page.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.1
 */
final class GDT_HR extends GDT
{
    public function renderCell() : string
    {
        return '<div class="gdt-hr"></div>';
    }
    
}
