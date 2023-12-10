<?php
namespace GDO\UI;

use GDO\Form\GDT_Submit;

/**
 * A button to show details.
 *
 * @author gizmore
 */
class GDT_ShowButton extends GDT_Button
{

	public string $icon = 'view';

	public function gdtDefaultName(): ?string
	{
		return 'show';
	}


}
