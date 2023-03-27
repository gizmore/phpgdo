<?php
namespace GDO\UI;

use GDO\Core\GDT_Enum;
use GDO\Core\GDT_Response;

/**
 * A selection for which navbar to use.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class GDT_PageBar extends GDT_Enum
{

	protected function __construct()
	{
		parent::__construct();
		$this->enumValues('none', 'left', 'right', 'bottom');
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		switch ($var)
		{
			case 'left':
				return GDT_Page::instance()->leftBar();
			case 'right':
				return GDT_Page::instance()->rightBar();
			case 'bottom':
				return GDT_Page::instance()->bottomBar();
			default:
				return GDT_Bar::make('none');
		}
	}

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		return true;
	}

}
