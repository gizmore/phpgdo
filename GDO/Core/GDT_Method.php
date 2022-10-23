<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;
use GDO\Language\Trans;
use GDO\User\GDO_User;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Repeat;

/**
 * A GDT_Method holds a Method and inputs to bind.
 * An input s either a string or a GDT_Method.
 * A method saves it response [WithResult.php]()
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
class GDT_Method extends GDT
{
	use WithName;
	use WithInput;
	use WithValue;
	use WithEnvironment;
	
	public GDT $result;
	
	private function getCLIAutoButton(array $inputs) : ?string
	{
		return $this->method->appliedInputs($inputs)->getAutoButton(array_keys($inputs));
	}
	
	/**
	 * Exexute this method.
	 * 
	 * @return GDT_Response
	 */
	public function execute()
	{
		if (!isset($this->result))
		{
			$this->changeUser();
			$inputs = $this->getInputs();
			if ($this->clibutton)
			{
				if ($button = $this->getCLIAutoButton($inputs))
				{
					$inputs[$button] = '1';
					Application::instance()->verb(GDT_Form::POST);
				}
				else
				{
					Application::instance()->verb(GDT_Form::GET);
				}
			}
			$this->result = $this->method->executeWithInputs($inputs, $this->withPermissionCheck);
		}
		return $this->result;
	}
	
	###########
	### CLI ###
	###########
	public bool $clibutton = false;
	
	/**
	 * Toggle if we should autodetect a button.
	 */
	public function clibutton(bool $clibutton = true) : self
	{
		$this->clibutton = $clibutton;
		return $this;
	}
	
	############
	### Perm ###
	############
	public bool $withPermissionCheck = true;
	
	/**
	 * Disable permission checking for executing this method.
	 */
	public function noChecks(bool $noChecks=true) : self
	{
		$this->withPermissionCheck = !$noChecks;
		return $this;
	}
	
	##################
	### File Cache ###
	##################
	/**
	 * Toggle to true for file cache.
	 */
	public function fileCached() { return false; }
	
	public function fileCacheExpire() { return GDO_MEMCACHE_TTL; }
	
	public function fileCacheKey()
	{
		$params = $this->gdoParameterCache();
		$p = '';
		foreach ($params as $gdt)
		{
			if ($gdt->name)
			{
				if ($v = $this->gdoParameterVar($gdt->name))
				{
					$p .= '_' . $gdt->name . '_' . FileUtil::saneFilename($v);
				}
			}
		}
		
		$fmt = Application::$INSTANCE->getFormat();
		return sprintf('method_%s_%s_%s_%s.%s',
			$this->getModuleName(), $this->getMethodName(),
			Trans::$ISO, $p, $fmt);
	}
	
	public function fileCacheKeyGroup()
	{
		return sprintf('method_%s_%s_',
			$this->getModuleName(), $this->getMethodName());
	}
	
	/**
	 * Get the cached content for this method, iso, fmt
	 * @return string|boolean
	 */
	public function fileCacheContent()
	{
		if (!$this->hasUserPermission(GDO_User::current()))
		{
			return false;
		}
		$key = $this->fileCacheKey();
		$content = Cache::fileGet($key, $this->fileCacheExpire());
		return $content;
	}
	
	public function fileUncache()
	{
		$start = $this->fileCacheKeyGroup();
		Filewalker::traverse(Cache::filePath(), null, function($entry, $path) use ($start){
			if (str_starts_with($entry, $start))
			{
				unlink($path);
			}
		});
	}
	
	##############
	### Pathes ###
	##############
	/**
	 * Get a path for this module in it's GDO folder.
	 * @param string $path
	 * @return string
	 */
	public function filePath($path='')
	{
		$module = $this->getModule();
		return $module->filePath($path);
	}
	
	/**
	 * Get the temp path for a method. Like temp/Module/Method/
	 * @param string $path - append
	 * @return string
	 */
	public function tempPath($path='')
	{
		$module = $this->getModule();
		$tempDir = $module->tempPath($this->gdoShortName());
		@mkdir($tempDir, GDO_CHMOD, true);
		$tempDir .= '/';
		return $tempDir . $path;
	}
	
	##############
	### Render ###
	##############
	public function render()
	{
		if (!isset($this->result))
		{
			$this->execute(false);
		}
		return $this->result->render();
	}
	
	public function addInput(?string $key, $var) : self
	{
		if (!isset($this->inputs))
		{
			$this->inputs = [];
		}
		
		$gdt = $this->getGDTForInputKey($key);
		if (!$gdt)
		{
			return $this;
		}
		
		$key = $gdt->getName();
		if ($gdt instanceof GDT_Repeat)
		{
			if (!isset($this->inputs[$key]))
			{
				$this->inputs[$key] = [];
			}
			$this->inputs[$key][] = $var;
		}
		else
		{
			$this->inputs[$key] = $var;
		}
		$gdt->inputs($this->inputs);
		return $this;
	}
		
	private int $positionalPosition = 0;
	
	private function getGDTForInputKey(?string $key) : ?GDT
	{
		if ($key !== null)
		{
			return $this->method->gdoParameter($key, false);
		}
		
		$last = null;
		$i = 0;
		foreach ($this->method->gdoParameterCache() as $gdt)
		{
			if ($gdt->isPositional())
			{
				if ($i === $this->positionalPosition)
				{
					$this->positionalPosition++;
					return $gdt;
				}
				$last = $gdt;
				$i++;
			}
		}
		return $last;
	}
	
}
