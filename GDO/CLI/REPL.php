<?php
declare(strict_types=1);
namespace GDO\CLI;

use GDO\Core\GDT;
use GDO\Core\Website;
use GDO\UI\TextStyle;

/**
 * Helper class for interactive CLI applications.
 *
 * @version 7.0.3
 * @since 7.0.2
 * @author gizmore
 */
final class REPL
{

	/**
	 * confirm prompt that exits on abort.
	 */
	public static function confirmOrDie(string $prompt, ?bool $default = true, string $abortmsg = 'Aborted', int $exitCode = 0): void
	{
		if (!self::confirm($prompt, $default))
		{
			die(self::abortmsg());
		}
	}

	/**
	 * DO a yes/no confirmation prompt.
	 */
	public static function confirm(string $prompt, ?bool $default, bool $allowNo = true): bool
	{
		$y = $default === true ? 'Y' : 'y';
		$n = $default === false ? 'N' : 'n';
		$a = $default === null ? 'A' : 'a';
		$an = $allowNo ? "/{$n}" : '';
		$prompt = "{$prompt} ({$y}{$an}/{$a}): ";
		if ($line = readline($prompt))
		{
			switch (strtolower($line[0]))
			{
				case 'y':
					return true;
				case 'n':
					return false;
				case 'a':
					die(self::abortmsg());
			}
		}
//		if ($default === null)
//		{
//			throw new GDO_Exception('err_repl_input');
//		}
		return !!$default;
	}

	private static function abortmsg(): string
	{
		return t('msg_abort');
	}

	public static function abortable(string $prompt): void
	{
		if (!self::confirm($prompt, null, false))
		{
			die(self::abortmsg());
		}
	}

	public static function acknowledge(string $prompt, ?bool $default = null): bool
	{
		return self::confirm($prompt, $default, false);
	}

	public static function changedGDTVar(GDT $gdt, string $prompt = ''): bool
	{
		$old = $gdt->getVar();
		self::changeGDT($gdt, $prompt);
		$new = $gdt->getVar();
		return !$gdt->hasError() && $old !== $new;
	}

	/**
	 * Show a prompt to change a GDT value.
	 * Validate and return success.
	 */
	public static function changeGDT(GDT $gdt, string $prompt = ''): bool
	{
		$xmplvars = (string)$gdt->gdoExampleVars();
		$prompt = $prompt ? "$prompt " : '';
		if ($label = $gdt->renderLabel())
		{
			$prompt .= $label;
			$prompt .= ' - ';
		}
		if ($tooltip = $gdt->renderIconText())
		{
			$prompt .= $tooltip;
			$prompt .= ' ';
		}
		$prompt .= self::xmplDefaults($gdt, $xmplvars);
		echo $prompt;
		echo '?: ';
		$response = rtrim(readline(), "\r\n");
		if ($response === '')
		{
			return false;
		}
//		$response = $response === '' ? null : $response;
		$gdt->var($response);
		if ($gdt->validate($gdt->getValue()))
		{
			return true;
		}
		Website::error('GDOv7', 'err_adm_iconfig', [$gdt->getName(), $gdt->renderError()]);
		return false;
	}

	private static function xmplDefaults(GDT $gdt, string $xmplvars): string
	{
//		$back = '';
		$xmplvars = str_replace('|', ',', $xmplvars);
		$xmplvars2 = ",{$xmplvars},";
		$initial = $gdt->getInitial();
		if (
			($initial !== null) &&
			(str_contains($xmplvars2, ",{$initial},"))
		)
		{
			$back = str_replace(",{$initial},", sprintf(',%s,', TextStyle::bold($initial)), $xmplvars2);
		}
		elseif ($initial !== null)
		{
			$initial = TextStyle::bold($initial);
			$back = ",{$xmplvars},$initial,";
		}
		else
		{
			$back = $xmplvars;
		}
		return trim($back, ',');
	}

}
