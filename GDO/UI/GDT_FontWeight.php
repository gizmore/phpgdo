<?php
namespace GDO\UI;

use GDO\Core\GDT_EnumNoI18n;

/**
 * Font weight enum select.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
class GDT_FontWeight extends GDT_EnumNoI18n
{
    public string $icon = 'font';
    
	public function defaultLabel(): static { return $this->label('font_weight'); }
	
}
