<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;
use GDO\Language\Trans;
use GDO\User\GDO_User;

/**
 * A GDT_Method holds a Method and inputs to bind.
 * An input s either a string or a GDT_Method.
 * A method saves it response [WithResult.php]()
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
class GDT_Method extends GDT
{
	use WithName;
	use WithInput;
	use WithValue;
	use WithEnvironment;
	
	public GDT $result;
	
	/**
	 * Exexute this method.
	 */
	public function execute(bool $withReset=true)
	{
		if (!isset($this->result))
		{
			$method = $this->method->withAppliedInputs($this->getInputs());
			# Call either with hooks and stuff or without
			if ($withReset)
			{
				Application::$INSTANCE->reset();
				$this->changeUser();
				$this->result = $method->exec();
			}
			else
			{
				$this->result = $method->execute();
			}
		}
		return $this->result;
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
	
}
