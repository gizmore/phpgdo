<?php
namespace GDO\UI;

use GDO\Form\GDT_Submit;

/**
 * An edit button.
 * 
 * @author gizmore
 */
class GDT_EditButton extends GDT_Submit
{
	public string $icon = 'edit';

	public function getDefaultName() : string { return 'edit'; }
    public function defaultLabel() : self { return $this->labelNone(); }
	
}
