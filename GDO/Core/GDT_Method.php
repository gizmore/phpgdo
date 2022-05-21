<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Util\FileUtil;
use GDO\File\Filewalker;
use GDO\Language\Trans;
use GDO\User\GDO_User;

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
	use WithFields;
	use WithEnvironment;
	
	public function execute()
	{
		return $this->changeUser()->method->inputs($_REQUEST)->exec();
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
		
		$fmt = Application::instance()->getFormat();
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
	
	
}
