<?php
namespace GDO\CLI;

use GDO\Core\GDT;

/**
 * Helper class for interactive CLI applications.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.2
 */
final class REPL 
{
	
	private static function abortmsg(): string
	{
		return t('msg_abort');
	}
	
	/**
	 * DO a yes/no confirmation prompt.
	 */
	public static function confirm(string $prompt, ?bool $default, bool $allowNo=true): bool
	{
		$y = $default === true ? 'Y' : 'y';
		$n = $default === false ? 'N' : 'n';
		$an = $allowNo ? "/{$n}" : '';
		$prompt = "{$prompt} ({$y}{$an}/a): ";
		echo $prompt;
		switch (@strtolower(trim(readline()))[0])
		{
			case 'y': return true;
			case 'n': return false;
			case 'a': die(self::abortmsg());
			default: return !!$default;
		}
	}

	/**
	 * confirm prompt that exits on abort.
	 */
	public static function confirmOrDie(string $prompt, ?bool $default=true, string $abortmsg='Aborted', int $exitCode=0): void
	{
		if (!self::confirm($prompt, $default))
		{
			die(self::abortmsg());
		}
	}
	
	public static function abortable(string $prompt): void
	{
		if (!self::confirm($prompt, null, false))
		{
			die(self::abortmsg());
		}
	}
	
	public static function acknowledge(string $prompt, ?bool $default): bool
	{
		return self::confirm($prompt, $default, false);
	}
	
	/**
	 * Show a prompt to change a GDT value.
	 * Validate and return success.
	 */
	public static function changeGDT(GDT $gdt, string $prompt=''): bool
	{
		$xmplvars = $gdt->gdoExampleVars();
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
		$prompt .= "({$xmplvars})";
		echo $prompt;
		$response = rtrim(readline(), "\r\n");
		if ($response === '')
		{
			return false;
		}
		$response = $response === '' ? null : $response;
		$gdt->var($response);
		return $gdt->validate($gdt->getValue());
	}
	
	/**
	 * 
	 * @param GDT $gdt
	 * @param string $prompt
	 * @return bool - has the user changed  the value?
	 */
	public static function changedGDTVar(GDT $gdt, string $prompt=''): bool
	{
		$old = $gdt->getVar();
		self::changeGDT($gdt, $prompt);
		$new = $gdt->getVar();
		return $gdt->hasError() ? false : $old !== $new;
	}
		
}
