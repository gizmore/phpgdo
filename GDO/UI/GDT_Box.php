<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;

/**
 *
 *
 * @version 7.0.1
 * @since 6.0.1
 * @author gizmore
 */
class GDT_Box extends GDT_Container
{

    use WithTitle;

	public bool $flex = true;
	public int $flexDirection = self::HORIZONTAL;
	public bool $flexWrap = true;
	public bool $flexShrink = false;

//	protected function setupHTML(): void
//	{
//		$this->addClass('gdt-box panel');
//		parent::setupHTML();
//	}

    public function renderHTML(): string
    {
        return GDT_Template::php('UI', 'box_html.php', ['field' => $this]);
    }

}
