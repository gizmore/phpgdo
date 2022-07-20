<?php
namespace GDO\UI;

use GDO\Core\GDT_Enum;

/**
 * A selection for which navbar to use.
 * 
 * @author gizmore
 * @version 7.0.1 
 */
final class GDT_PageBar extends GDT_Enum
{
	protected function __construct()
	{
		parent::__construct();
		$this->enumValues('none', 'left', 'right', 'bottom');
	}
	
	public function toValue(string $var = null)
	{
		switch ($var)
		{
			case 'left': return GDT_Page::instance()->leftBar();
			case 'right': return GDT_Page::instance()->rightBar();
			case 'bottom': return GDT_Page::instance()->bottomBar();
			default: return GDT_Null::make('none');
		}
	}

	public function validate($value) : bool
	{
		return true;
	}
	
}
