<?php
namespace GDO\UI;

use GDO\Form\GDT_Submit;

/**
 * An add button.
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 * @see GDT_Submit
 */
class GDT_AddButton extends GDT_Submit
{

	protected function __construct()
	{
		parent::__construct();
// 		$this->label('add');
		$this->icon = 'add';
	}

	public function gdtDefaultLabel(): ?string
	{
		return 'btn_add';
	}

	public function gdtDefaultName(): ?string
	{
		return 'add';
	}

}
