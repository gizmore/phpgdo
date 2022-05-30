<?php
namespace GDO\UI;

class GDT_EditButton extends GDT_Button
{
    public function getDefaultName() : string { return 'edit'; }
    public function defaultLabel() : self { return $this->label('btn_edit'); }
    
	public $icon = 'edit';
	
}
