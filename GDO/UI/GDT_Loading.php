<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A backdrop loading spinner.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
final class GDT_Loading extends GDT
{
    public function renderHTML() : string
    {
    	return GDT_Template::php('UI', 'loading_html.php', ['field' => $this]);
    }

}
