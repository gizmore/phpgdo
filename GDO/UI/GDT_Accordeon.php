<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;

/**
 * A panel that collapses on click.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.0
 */
final class GDT_Accordeon extends GDT_Container
{
	use WithTitle;
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
    {
        return GDT_Template::php('UI', 'accordeon_html.php', ['field' => $this]);
    }
    
    public function renderForm() : string
    {
    	return $this->renderHTML();
    }
    
    ##############
    ### Opened ###
    ##############
    public bool $opened = false;
    public function opened(bool $opened=true) : self
    {
        $this->opened = $opened;
        return $this;
    }
    
    public function closed(bool $closed=true) : self
    {
    	return $this->opened(!$closed);
    }
    
}
