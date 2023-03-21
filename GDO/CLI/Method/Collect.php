<?php
namespace GDO\CLI\Method;

use GDO\CLI\MethodCLI;
use GDO\Core\GDT_Path;
use GDO\Core\GDT_String;
use GDO\Core\Website;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;

/**
 * Copy all files from all subdirectories in the path to the path itself.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class Collect extends MethodCLI
{

	public function isTrivial(): bool { return false; }

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_String::make('pattern'),
			GDT_Path::make('path')->existingDir()->notNull(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form)
	{
		$path = $this->getPath();
		$pattern = $this->getPattern();
		$callback = [$this, 'callbackPath'];
		Filewalker::traverse($path, $pattern, $callback, null, 100, $path);
	}

	public function getPath(): string
	{
		return $this->gdoParameterVar('path');
	}

	public function getPattern(): ?string
	{
		return $this->gdoParameterVar('pattern');
	}

	public function callbackPath(string $entry, string $fullpath, string $path): void
	{
		$newpath = "{$path}/{$entry}";
		if (!FileUtil::isFile($newpath))
		{
			Website::message('Find', 'msg_cli_collect_file', [$entry]);
			rename($fullpath, $newpath);
		}
		else
		{
			Website::message('Find', 'msg_cli_skip_file', [$entry]);
		}
	}

}
