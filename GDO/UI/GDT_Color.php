<?php
namespace GDO\UI;

use GDO\Core\GDT_String;

/**
 * Color selection input.
 * Include the hash/pound character in DB to keep readability.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
class GDT_Color extends GDT_String
{
	public function defaultLabel() : self
	{
		return $this->label('color');
	}
	
	##############
	### String ###
	##############
	public int $min = 4;
	public int $max = 7;
	public string $icon = 'color';
	public string $pattern = '/^#(?:[a-z0-9]{3}){1,2}$/iD';

	public function getInputType() : string
	{
		return 'color';
	}
	
	public function plugVars() : array
	{
		return [
			[$this->getName() => '#fF0000'],
		];
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		$hx = $this->getValue();
		$fg = Color::fromHex($hx)->complementary()->asHex();
		return '<div class="gdt-color" style="background: '.$hx.'; color: '.$fg.';">'.$hx.'</div>';
	}

	###############
	### Static ####
	###############
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
