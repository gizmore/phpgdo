<?php
namespace GDO\UI;

/**
 * Flex class handling trait for containers.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.3.0
 * 
 * @see GDT_Bar
 * @see GDT_Box
 * @see GDT_Container
 */
trait WithFlex
{
    #################
    ### Paramters ###
    #################
    public bool $flex = false;
    public bool $flexCollapse = false;
    public int $flexDirection = self::HORIZONTAL;
    
    /**
     * Enable flex for this container.
     * 
     * @param boolean $flex
     * @return self
     */
    public function flex(bool $flex=true, bool $collapse=false) : self
    {
        $this->flex = $flex;
        $this->flexCollapse = $collapse;
        return $this;
    }
    
    #################
    ### Direction ###
    #################
    public function vertical(bool $collapse=false) : self
    {
        $this->flexDirection = self::VERTICAL;
        return $this->flex(true, $collapse);
    }
    
    public function horizontal($collapse=false)
    {
        $this->flexDirection = self::HORIZONTAL;
        return $this->flex(true, $collapse);
    }
    
    ##############
    ### Render ###
    ##############
    /**
     * Render classname for flex classes.
     * @return string
     */
    public function flexClass() : string
    {
        return $this->flexDirection === self::HORIZONTAL ?
        	'flx-row' : 'flx-column';
    }

}
