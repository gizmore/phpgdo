<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Language\Trans;
use GDO\UI\GDT_HTML;

/**
 * This method decorator adds file cache behaviour to a method.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 * @see Method
 */
trait WithFileCache
{
	public int $fileCacheExpire = GDO_MEMCACHE_TTL;
	public function fileCacheExpire(int $expire) : self
	{
		$this->fileCacheExpire = $expire;
		return $this;
	}
	
	protected function fileCacheKey()
	{
		$key = $this->getModuleName();
		$key .= ':';
		$key .= $this->getMethodName();
		$key .= ':';
		$key .= Trans::$ISO;
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$key .= ':';
			$key = $gdt->getVar();
		}
		return $key;
	}
	
	protected function executeB()
	{
		$key = $this->fileCacheKey();
		if ($content = Cache::fileGet($key, $this->fileCacheExpire))
		{
			# Cache hit :)
			return GDT_HTML::make()->var($content);
		}
		# No cache
		return $this->execute();
	}
	
}
