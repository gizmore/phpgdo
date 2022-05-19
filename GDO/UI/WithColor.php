<?php
namespace GDO\UI;

/**
 * Add a color attribute to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.4
 */
trait WithColor
{
    public string $colorFG;
    public string $colorBG;
    
    public function color(string $colorFG, string $colorBG)
    {
        return $this->colorFG($colorFG)->colorBG($colorBG);
    }
    
    public function colorFG($colorFG)
    {
        $this->colorFG = $colorFG;
        return $this;
    }
    
    public function colorBG($colorBG)
    {
        $this->colorBG = $colorBG;
        return $this;
    }
    
}
