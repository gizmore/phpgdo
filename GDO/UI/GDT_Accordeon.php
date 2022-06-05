<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A panel that collapses monclick.
 * Add 
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.0
 */
final class GDT_Accordeon extends GDT_Container
{
	use WithTitle;
	
// 	public array $titles = [];
// 	public array $sections = [];
	
// 	public function addSection(string $title, GDT $section) : self
// 	{
// 		$this->titles[] = $title;
// 		$this->sections[] = $section;
// 		return $this;
// 	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
    {
        return GDT_Template::php('UI', 'accordeon_html.php', ['field' => $this]);
    }
    
    ##############
    ### Opened ###
    ##############
    public bool $opened = false;
    public function opened($opened=true)
    {
        $this->opened = $opened;
        return $this;
    }
    
    public function closed($closed=true)
    {
    	return $this->opened(!$closed);
    }
    
}
