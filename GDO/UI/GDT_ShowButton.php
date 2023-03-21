<?php
namespace GDO\UI;

/**
 * A button to show details.
 *
 * @author gizmore
 */
class GDT_ShowButton extends GDT_Button
{

	public string $icon = 'show';

	public function getDefaultName(): string
	{
		return 'show';
	}

}
