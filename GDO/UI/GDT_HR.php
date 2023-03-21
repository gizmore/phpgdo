<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * An <HR> element, but it uses a span to render in html. In CLI it renders ascii art.
 *
 * @version 7.0.1
 * @since 6.10.1
 * @author gizmore
 */
final class GDT_HR extends GDT
{

	public function renderCLI(): string
	{
		return str_repeat('-', 48) . "\n";
	}

	public function renderHTML(): string
	{
		return '<span class="gdt-hr"></span>';
	}

}
