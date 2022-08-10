<?php
namespace GDO\UI;

/**
 * An edit button.
 * 
 * @author gizmore
 */
class GDT_EditButton extends GDT_Button
{
	public string $icon = 'edit';

	public function getDefaultName() : string { return 'edit'; }
    public function defaultLabel() : self { return $this->labelNone(); }
	
}
