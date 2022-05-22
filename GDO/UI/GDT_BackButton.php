<?php
namespace GDO\UI;

use GDO\Core\Website;

/**
 * A button to navigate back.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.3
 */
final class GDT_BackButton extends GDT_Button
{
	protected function __construct()
	{
		parent::__construct();
		$this->icon('back');
		$this->label('btn_back');
		$this->href(Website::hrefBack());
	}
	
}
