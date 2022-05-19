<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_String;


/**
 * Color selection type.
 * Include hash character(#) in db var.
 *
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_Color extends GDT_String
{
	public int $min = 4;
	public int $max = 7;
	public string $pattern = "/^#(?:[a-z0-9]{3}){1,2}$/i";

	public function defaultLabel() : self
	{
		return $this->label('color');
	}

	public function renderForm() : string
	{
		return GDT_Template::php('UI', 'form/color.php', [
			'field' => $this
		]);
	}

	public function renderCell() : string
	{
		return GDT_Template::php('UI', 'cell/color.php', [
			'field' => $this
		]);
	}

	public static function html2rgb($input)
	{
		$input = $input[0] === '#' ? substr($input, 1, 6) : substr($input, 0, 6);
		return [
			hexdec(substr($input, 0, 2)),
			hexdec(substr($input, 2, 2)),
			hexdec(substr($input, 4, 2))
		];
	}

}
