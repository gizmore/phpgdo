<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\Application;
use GDO\Core\GDT;

/**
 * Color utility and conversion object.
 *
 * @version 7.0.3
 * @since 6.5.0
 */
final class Color
{

	###############
	### Utility ###
	###############
	private int $r;
	private int $g;
	private int $b;

	/**
	 * Colors are 0 - 255.
	 */
	public function __construct(int $r, int $g, int $b)
	{
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}


	public static function green(string $s): string
	{
		return self::colored($s, '#002d00', "\033[32m", '03');
	}


	public static function red(string $s): string
	{
		return self::colored($s, 'red', "\033[31m", '04');
	}


	##############
	### Object ###
	##############

	public static function colored(string $s, string $colorHTML, string $colorCLI, string $colorIRC): string
	{
		switch (Application::$MODE)
		{
			case GDT::RENDER_NIL: # These renderers have no colors!
			case GDT::RENDER_GTK:
			case GDT::RENDER_JSON:
			case GDT::RENDER_XML:
			case GDT::RENDER_BINARY:
				return $s;

			case GDT::RENDER_CLI:
				return "{$colorCLI}{$s} \033[0m";

			case GDT::RENDER_IRC:
				// IRC is not part of phpgdo core
				return module_enabled('DogIRC') ?
					\GDO\DogIRC\IRCLib::colored($s, $colorIRC) :
					$s;

			default: # HTML
				return sprintf('<span style="color: %s;">%s</span>', $colorHTML, $s);
		}
	}

	/**
	 * Array[255,255,255]
	 * @return int[]
	 */
	public function asRGB(): array { return [$this->r, $this->g, $this->b]; }

	public function asHex(): string { return sprintf('#%02x%02x%02x', $this->r, $this->g, $this->b); }

	public function complementary(): self
	{
		if (($this->r == 0) && ($this->g == 0) && ($this->b == 0))
		{
			return self::fromHex('#ffffff');
		}
		[$h, $s, $v] = $this->asHSV();
		return self::fromHSV($this->hueShift($h, 180), $s, $v);
	}

	public static function fromHex(string $hex): self
	{
		$matches = null;
		if (preg_match('/^#?([a-f0-9]{1,2})([a-f0-9]{1,2})([a-f0-9]{1,2})$/iD', $hex, $matches))
		{
			return new self(hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
		}
		else
		{
			throw new \Error('Cannot parse hex color ' . $hex);
		}
	}

	/**
	 * @return float[]
	 */
	public function asHSV(): array
	{
		$h = 0;
		$r = $this->r;
		$g = $this->g;
		$b = $this->b;
		$max = max($r, $g, $b);
		$dif = $max - min($r, $g, $b);
		$s = $max === 0 ? 0 : (100 * $dif / $max);
		if ($s === 0)
		{
			$h = 0;
		}
		elseif ($dif === 0.0)
		{
			$h = 360;
		} # FIXME: this fixes a crash but all is same color :(
		elseif ($r === $max)
		{
			$h = 60.0 * ($g - $b) / $dif;
		}
		elseif ($g === $max)
		{
			$h = 120.0 + 60.0 * ($b - $r) / $dif;
		}
		elseif ($b === $max)
		{
			$h = 240.0 + 60.0 * ($r - $g) / $dif;
		}
		if ($h < 0)
		{
			$h += 360;
		}
		$v = round($max * 100 / 255.0);
		$h = round($h);
		$s = round($s);
		return [$h, $s, $v];
	}

//	private function max3($a, $b, $c) { return ($a > $b) ? (($a > $c) ? $a : $c) : (($b > $c) ? $b : $c); }
//
//	private function min3($a, $b, $c) { return ($a < $b) ? (($a < $c) ? $a : $c) : (($b < $c) ? $b : $c); }

	###########
	### CLI ###
	###########

	public static function fromHSV(float $h, float $s, float $v): self
	{
		[$r, $g, $b] = self::hsvToRGB($h, $s, $v);
		return new self($r, $g, $b);
	}

	/**
	 * The HSV floats are in range 0-1
	 * @return int[]
	 */
	public static function hsvToRGB(float $h, float $s, float $v): array
	{
		if ($s === 0)
		{
			$r = $g = $b = (int) round($v * 2.55);
		}
		else
		{
			$h /= 60.0;
			$s /= 100.0;
			$v /= 100.0;
			$i = floor($h);
			$f = $h - $i;
			$p = $v * (1 - $s);
			$q = $v * (1 - $s * $f);
			$t = $v * (1 - $s * (1 - $f));
			switch ($i)
			{
				case 0:
					$r = $v;
					$g = $t;
					$b = $p;
					break;
				case 1:
					$r = $q;
					$g = $v;
					$b = $p;
					break;
				case 2:
					$r = $p;
					$g = $v;
					$b = $t;
					break;
				case 3:
					$r = $p;
					$g = $q;
					$b = $v;
					break;
				case 4:
					$r = $t;
					$g = $p;
					$b = $v;
					break;
				default:
					$r = $v;
					$g = $p;
					$b = $q;
					break;
			}
			$r = (int) round($r * 255);
			$g = (int) round($g * 255);
			$b = (int) round($b * 255);
		}
		return [$r, $g, $b];
	}

	private function hueShift(float $h, float $s): float
	{
		$h += $s;
		while ($h >= 360.0)
		{
			$h -= 360.0;
		}
		while ($h < 0.0)
		{
			$h += 360.0;
		}
		return $h;
	}

}
