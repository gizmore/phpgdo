<?php
namespace GDO\UI;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\GDT;

/**
 * A utility class that renders strings with a text style.
 * Depending on rendering mode.
 * CLI does use bash stuff, HTML uses CSS style.
 * For coloring text, see the UI Color class.
 *
 * @TODO What about IRC and co?
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 * @see Color
 */
final class TextStyle
{

	public static function blink(string $s): string
	{
		return self::display($s, 'span class="gdt-blink"', 'span', 'blink');
	}

	private static function display(string $s, string $tagStart, string $tagEnd, string $cliMethod): string
	{
		$app = Application::$INSTANCE;
		switch (Application::$MODE)
		{
// 			case GDT::RENDER_IRC:
// 				return call_user_func([CLI::class, $cliMethod], $s);
			case GDT::RENDER_CLI:
				return call_user_func([CLI::class, $cliMethod], $s);
			default:
				return $app->isHTML() ? "<{$tagStart}>{$s}</{$tagEnd}>" : $s;
		}
	}

	public static function boldi(string $s): string
	{
		return self::bold(self::italic($s));
	}

	public static function bold(string $s): string
	{
		return self::display($s, 'b', 'b', 'bold');
	}

	public static function italic(string $s): string
	{
		return self::display($s, 'i', 'i', 'italic');
	}

	###############
	### Private ###
	###############

	public static function underline(string $s): string
	{
		return self::display($s, 'span style="text-decoration: underline;"', 'span', 'underline');
	}

}
