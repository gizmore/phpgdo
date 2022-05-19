<?php
namespace GDO\UI;

/**
 * A download button with label and icon.
 * Adds gdt-download class.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.1
 */
final class GDT_DownloadButton extends GDT_Button
{
    public function defaultLabel() : self { return $this->label('btn_download'); }
    
    protected function __construct()
    {
        parent::__construct();
        $this->name = "download";
        $this->icon('download');
        $this->addClass('gdt-download-button');
    }
    
}
