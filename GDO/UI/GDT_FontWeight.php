<?php
namespace GDO\UI;

use GDO\Core\GDT_EnumNoI18n;

/**
 * Font weight enum select.
 *
 * @version 6.10
 * @since 6.10
 * @author gizmore
 */
class GDT_FontWeight extends GDT_EnumNoI18n
{

	public string $icon = 'font';

	public function gdtDefaultLabel(): ?string { return 'font_weight'; }

}
