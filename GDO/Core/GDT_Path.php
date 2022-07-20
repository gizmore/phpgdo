<?php
namespace GDO\Core;

use GDO\Util\FileUtil;

/**
 * A path variable with existance validator.
 * 
 * @TODO: Make a GDT_PathCompleted that is GDT_ComboBox with auto completion.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
final class GDT_Path extends GDT_String
{
	public string $pattern = "#^[^?!]+$#iD";
	
	public function defaultLabel() : self { return $this->label('path'); }
	
	public function htmlClass() : string
	{
		return FileUtil::isFile($this->getValue()) ? ' gdo-file-valid' : ' gdo-file-invalid';
	}
	
	#################
	### Existance ###
	#################
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
		return true;
	}
	
	public function validatePath(string $filename) : bool
	{
		if ($this->existing)
		{
			if ( (!is_readable($filename)) || (!call_user_func($this->existing, $filename)) )
			{
				return $this->error('err_path_not_exists', [$filename, t($this->existing)]);
			}
		}
		return true;
	}
	
	/**
	 * The GDOv7-LICENSE file should exist. Good default plug.
	 */
	public function plugVar() : string
	{
		return GDO_PATH . 'LICENSE';
	}

}
