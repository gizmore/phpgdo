<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_String;

/**
 * Color selection input.
 * Include the hash/pound character in DB to keep readability.
 *
 * @version 7.0.3
 * @since 6.1.0
 * @author gizmore
 */
class GDT_Color extends GDT_String
{

	public ?int $min = 4;

	##############
	### String ###
	##############
	public ?int $max = 7;
	public string $icon = 'color';
	public string $pattern = '/^#(?:[a-z0-9]{3}){1,2}$/iD';

	public static function html2rgb($input): array
	{
		$input = $input[0] === '#' ? substr($input, 1, 6) : substr($input, 0, 6);
		return [
			hexdec(substr($input, 0, 2)),
			hexdec(substr($input, 2, 2)),
			hexdec(substr($input, 4, 2)),
		];
	}

	public function gdtDefaultLabel(): ?string
	{
		return 'color';
	}

	public function getInputType(): string
	{
		return 'color';
	}

	##############
	### Render ###
	##############

	public function plugVars(): array
	{
		return [
			[$this->getName() => '#fF0000'],
		];
	}

	###############
	### Static ####
	###############

	public function renderHTML(): string
	{
		if ($hx = $this->getValue())
		{
			$fg = Color::fromHex($hx)->complementary()->asHex();
			return '<div class="gdt-color" style="background: ' . $hx . '; color: ' . $fg . ';">' . $hx . '</div>';
		}
		else
		{
			return '<div class="gdt-color" style="background: #eee; color: #333;">' . t('none') . '</div>';
		}
	}

	public function renderCell(): string
	{
		if (!($hex = $this->getValue()))
		{
			return $this->renderHTML();
		}
		if (strlen($hex) === 4)
		{
			$hex = "#{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}{$hex[3]}{$hex[3]}";
		}
		[$red, $green, $blue] = Color::fromHex($hex)->asRGB();
		$background = (0.2126 * $red + 0.7152 * $green + 0.0722 * $blue) > 128 ? '#000' : '#fff';
		return '<div class="gdt-color" style="background: ' . $background . '; color: ' . $hex . ';">' . $hex . '</div>';
	}

}
