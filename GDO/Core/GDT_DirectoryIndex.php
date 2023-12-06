<?php
namespace GDO\Core;

use GDO\Net\GDT_Url;

final class GDT_DirectoryIndex extends GDT_Url
{

    public function renderCell(): string
    {
        /** @var GDO_DirectoryIndex $di */
        $di = $this->gdo;
        $this->var = $di->href_file_name();
        $this->titleRaw($di->getFileName());
        return parent::renderHTML();
    }

}
