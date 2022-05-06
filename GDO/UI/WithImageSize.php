<?php
namespace GDO\UI;

/**
 * Add image size attributes to a GDT.
 * 
 * @see GDO_File
 * @see GDT_File
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
trait WithImageSize
{
    public int $imageWidth = 32;
    public int $imageHeight = 32;
    
    public function imageSize(int $w, int $h=null) : self
    {
        $this->imageWidth = $w;
        $this->imageHeight = $h ? $h : $w;
        return $this;
    }
    
}
