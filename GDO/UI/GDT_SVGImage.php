<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\WithName;

/**
 * HTML SVG Image element.
 * Requires special <object> markup for external fonts.
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 */
final class GDT_SVGImage extends GDT_Image
{

	use WithName;
	use WithPHPJQuery;

	public const SVG = 'image/svg+xml';

	public function isTestable(): bool
	{
		return false;
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'image_svg.php', ['field' => $this]);
	}

}
