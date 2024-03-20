<?php
namespace GDO\Net;

/**
 *
 */
final class GDT_HTTP extends GDT_Url
{

    public function renderHTML(): string
    {
        return HTTP::getFromURL($this->getAbsoluteURL()->raw);
    }

}
