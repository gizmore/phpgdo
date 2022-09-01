<?php
namespace GDO\Core;

use GDO\Util\FileUtil;
use GDO\UI\TextStyle;

/**
 * A path variable with existance validator.
 * 
 * @TODO: Make a GDT_PathCompleted that is GDT_ComboBox with auto completion.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
final class GDT_Path extends GDT_ComboBox
{
	public string $pattern = "/^[^?!]+$/iD";
	
	public function defaultLabel() : self { return $this->label('path'); }
	
	public function htmlClass() : string
	{
		return FileUtil::isFile($this->getValue()) ? ' gdo-file-valid' : ' gdo-file-invalid';
	}
	
	#################
	### Existance ###
	#################
	/**
	 * @var bool|callable
	 */
	public $existing = false;
	public function existingDir() : self { $this->existing = 'is_dir'; return $this->icon('folder'); }
	public function existingFile() : self { $this->existing = 'is_file'; return $this->icon('file'); }

	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
				if (!$this->validatePath($value))
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
		return true;
	}
	
	public function validatePath(string $filename) : bool
	{
		if ($this->existing)
		{
			if ( (!is_readable($filename)) ||
				(!call_user_func($this->existing, $filename)) )
			{
				return $this->error('err_path_not_exists', [
					TextStyle::bold(html($filename)),
					t($this->existing)]
				);
			}
		}
		return true;
	}
	
	/**
	 * The GDOv7-LICENSE file should exist. Good default plug.
	 */
	public function plugVars() : array
	{
		return [
			[$this->getName() => (GDO_PATH . 'LICENSE')],
		];
	}
	
}
