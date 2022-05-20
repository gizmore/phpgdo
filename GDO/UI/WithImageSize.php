<?php
namespace GDO\UI;

/**
 * Add image size attributes to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 * @see GDO_File
 * @see GDT_File
 */
trait WithImageSize
{
    public int $imageWidth = 32;
    public int $imageHeight = 32;
    
    public function imageSize(int $w, int $h=0) : self
    {
        $this->imageWidth = $w;
        $this->imageHeight = $h ? $h : $w;
        return $this;
    }
    
}
