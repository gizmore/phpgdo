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
	
// 	protected function __construct()
// 	{
// 		parent::__construct();
// 	}
	
	#################
	### Existance ###
	#################
	/**
	 * @var bool|callable
	 */
	public $existing = false;
	public function existingDir() : self
	{
		$this->existing = 'is_dir';
		return $this->icon('folder');
	}
	
	public function existingFile() : self
	{
		$this->existing = 'is_file';
		return $this->icon('file');
	}

	##################
	### Completion ###
	##################
	public bool $completion = false;
	public function completion(bool $completion=true) : self
	{
		$this->completion = $completion;
		return $this;
	}
	
	private function setupCompletionHref()
	{
		switch ($this->existing)
		{
			case 'is_dir': $append = "&check=is_dir"; break;
			case 'is_file': $append = "&check=is_file"; break;
			default: $append = "&check=any"; break;
		}
		return $this->completionHref(href('Core', 'PathCompletion', $append));
	}
	
	##############
	### Render ###
	##############
	public function renderForm() : string
	{
		unset($this->completionHref);
		if ($this->completion)
		{
			$this->setupCompletionHref();
		}
		if (isset($this->completionHref))
		{
			return GDT_Template::php('Core', 'object_completion_form.php', ['field' => $this]);
		}
		return parent::renderForm();
	}

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
