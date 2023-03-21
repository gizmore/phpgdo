<?php
namespace GDO\UI;

/**
 * A button to navigate back.
 *
 * @version 7.0.0
 * @since 6.1.3
 * @author gizmore
 */
final class GDT_BackButton extends GDT_Button
{

	protected function __construct()
	{
		parent::__construct();
		$this->icon('back');
		$this->label('btn_back');
		$this->href(GDT_Redirect::hrefBack());
	}

}
