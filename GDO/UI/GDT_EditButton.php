<?php
namespace GDO\UI;

class GDT_EditButton extends GDT_Button
{
    public function getDefaultName() : string { return 'edit'; }
    public function defaultLabel() : self { return $this->noLabel(); }
    
	public string $icon = 'edit';
	
}
