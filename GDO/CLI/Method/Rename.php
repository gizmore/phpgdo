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
 * @example gdo cli.rename 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.2
 */
final class Rename extends MethodCLI
{
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Path::make('path')->existingDir()->notNull(),
			GDT_RegEx::make('pattern')->notNull(),
			GDT_String::make('replace')->notNull(),
			GDT_Checkbox::make('dirs')->initial('0'),
			GDT_Checkbox::make('files')->initial('1'),
			GDT_Checkbox::make('recursive')->initial('1'),
			);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		self::massRename(
			$form->getFormVar('path'),
			$form->getFormVar('pattern'),
			$form->getFormVar('replace'),
			$form->getFormValue('recursive'),
			$form->getFormValue('dirs'),
			$form->getFormValue('files'),
			);
		return $this->message('msg_done');
	}
	
	public static function massRename(string $path, string $pattern, string $replace, bool $recursive=false, bool $dirs=false, bool $files=true): void
	{
		$callbackDir = $dirs ? [self::class, 'rename'] : null;
		$callbackFile = $files ? [self::class, 'rename'] : null;
		$rec = $recursive ? 256 : 0;
		$args = [$pattern, $replace];
		Filewalker::traverse($path, $pattern, $callbackFile, $callbackDir, $rec, $args);
	}
	
	public static function rename(string $entry, string $fullpath, $args=null) : void
	{
		list($pattern, $replacement) = $args;
		$newEntry = preg_replace($pattern, $replacement, $entry);
		$newPath = substr($fullpath, 0, -strlen($entry));
		$newPath .= $newEntry;
		rename($fullpath, $newPath);
		Website::message(self::gdoHumanNameS(), 'msg_file_renamed', [html($newEntry)]);
	}
	
}
