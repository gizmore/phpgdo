<?php
namespace GDO\CLI\Method;

use GDO\CLI\MethodCLI;
use GDO\Form\GDT_Form;
use GDO\Core\GDT_Path;
use GDO\Core\GDT_RegEx;
use GDO\Form\GDT_Submit;
use GDO\Util\Filewalker;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Checkbox;
use GDO\Core\Website;

/**
 * Mass rename utility.
 * 
 * @example gdo cli.rename .,^(.*\.)mp3$,$1ogg
 * @example gdo cli.rename --pretend=1,.,DRAFT.md,README.md
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.2
 */
final class Rename extends MethodCLI
{
	
	/**
	 * Disable unit test fuzzer. dangerous for mass rename!
	 */
	public function isTrivial(): bool
	{
		return false;
	}
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Path::make('path')->existingDir()->notNull(),
			GDT_RegEx::make('pattern')->notNull()->tooltip('tt_cli_rename_pattern'),
			GDT_String::make('replace')->notNull()->tooltip('tt_cli_rename_replace'),
			GDT_Checkbox::make('pretend')->initial('0'),
			GDT_Checkbox::make('dirs')->initial('0'),
			GDT_Checkbox::make('files')->initial('1'),
			GDT_Checkbox::make('recursive')->initial('1'),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$count = self::massRename(
			$form->getFormVar('path'),
			$form->getFormVar('pattern'),
			$form->getFormVar('replace'),
			$form->getFormValue('recursive'),
			$form->getFormValue('dirs'),
			$form->getFormValue('files'),
			$form->getFormValue('pretend'),
		);
		return $this->message('msg_files_renamed', [$count]);
	}
	
	public static function massRename(string $path, string $pattern, string $replace, bool $recursive=false, bool $dirs=false, bool $files=true, bool $pretend=false): void
	{
		$callbackDir = $dirs ? [self::class, 'rename'] : null;
		$callbackFile = $files ? [self::class, 'rename'] : null;
		$rec = $recursive ? 256 : 0;
		$args = [$pattern, $replace, $pretend];
		Filewalker::traverse($path, $pattern, $callbackFile, $callbackDir, $rec, $args);
	}
	
	public static function rename(string $entry, string $fullpath, $args=null): int
	{
		$count = 0;
		list($pattern, $replacement, $pretend) = $args;
		$newEntry = preg_replace($pattern, $replacement, $entry);
		$newPath  = substr($fullpath, 0, -strlen($entry));
		$newPath .= $newEntry;
		if (!$pretend)
		{
			$count++;
			rename($fullpath, $newPath);
		}
		Website::message(self::gdoHumanNameS(),
			'msg_file_renamed',
			[html($fullpath), html($newPath)]);
		return $count;
	}
	
}