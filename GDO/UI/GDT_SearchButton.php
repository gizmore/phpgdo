<?php
namespace GDO\UI;

use GDO\Form\GDT_Submit;

/**
 * A search button.
 * 
 * @author gizmore
 */
class GDT_SearchButton extends GDT_Submit
{
	public string $icon = 'search';

	public function getDefaultName() : string { return 'search'; }
	
}
