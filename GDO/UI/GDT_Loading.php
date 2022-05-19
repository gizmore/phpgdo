<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * 
 * @author gizmore
 *
 */
final class GDT_Loading extends GDT
{
    public function renderCell() : string { return GDT_Template::php('UI', 'cell/loading.php', ['field' => $this]); }

}
