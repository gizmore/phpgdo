<?php
namespace GDO\UI;

use GDO\File\FileUtil;
use GDO\Core\GDT_Select;
use GDO\Core\GDT_Template;
use GDO\Util\Strings;

/**
 * Scan the fonts dir for a select.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.3
 */
class GDT_Font extends GDT_Select
{
    public $icon = 'font';
    
	public function defaultLabel() : self { return $this->label('font'); }
	
	public function __construct()
	{
	    parent::__construct();
	    $this->choices($this->fontChoices());
	}
	
	public function renderForm() : string
	{
		$this->choices = $this->fontChoices();
		return parent::renderForm();
	}
	
	public function validate($value) : bool
	{
		$this->choices = $this->fontChoices();
		return parent::validate($value);
	}
	
	public function fontChoices()
	{
		static $choices;
		if (!isset($choices))
		{
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
		}
		return $choices;
	}

}
