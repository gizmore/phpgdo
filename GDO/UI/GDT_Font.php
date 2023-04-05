<?php
namespace GDO\UI;

use GDO\Core\GDT_Select;
use GDO\Core\GDT_Template;
use GDO\Util\FileUtil;
use GDO\Util\Strings;

/**
 * Scan the font dirs for a select.
 * Fonts are placed in the thm/<theme>/fonts folders.
 * Only TTF is supported?
 *
 * @version 7.0.1
 * @since 6.0.3
 * @author gizmore
 */
class GDT_Font extends GDT_Select
{

	public string $icon = 'font';

	public function defaultLabel(): self { return $this->label('font'); }

 	protected function __construct()
 	{
 	    parent::__construct();
  	    $this->initChoices();
 	}

// 	public function renderForm() : string
// 	{
// // 		$this->choices = $this->fontChoices();
// 		return parent::renderForm();
// 	}

// 	public function validate($value) : bool
// 	{
// // 		$this->choices = $this->fontChoices();
// 		return parent::validate($value);
// 	}

// 	public function renderCLI() : string
// 	{
// 		return parent::renderCLI();
// 	}

	public function getChoices(): array
	{
// 		static $choices;
// 		if (!isset($choices))
// 		{
		$choices = [];
		foreach (GDT_Template::$THEMES as $path)
		{
			$dir = $path . 'fonts';
			if (FileUtil::isDir($dir))
			{
				$files = FileUtil::scandir($dir);
				foreach ($files as $file)
				{
					$fontPath = Strings::rsubstrFrom($dir . '/' . $file, GDO_PATH);
					$fontName = Strings::rsubstrTo($file, '.');
					$choices[$fontPath] = $fontName;
				}
			}
		}
// 		}
		return $choices;
	}

	public function plugVars(): array
	{
		return [
			[$this->name => 'GDO/Core/thm/default/fonts/arial.ttf'],
		];
	}

}
