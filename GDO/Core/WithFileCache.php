<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\Language\Trans;
use GDO\UI\GDT_HTML;

/**
 * This method decorator adds file cache behaviour to a method.
 * File cache key is generated from all Method::gdoParameterCache()
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 * @see Cache
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
		$sep = ';';
		$key = $this->getModuleName();
		$key .= $sep;
		$key .= $this->getMethodName();
		$key .= $sep;
		$key .= Trans::$ISO;
		$key .= $sep;
		$key .= Application::$MODE;
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$key .= $sep;
			$key .= $gdt->getVar();
		}
		return $key;
	}
	
	protected function isFileCacheEnabled() : bool
	{
		return true;
	}
	
	protected function executeB()
	{
		if (!$this->isFileCacheEnabled())
		{
			return $this->execute();
		}
		
		$key = $this->fileCacheKey();
		if ($content = Cache::fileGetSerialized($key, $this->fileCacheExpire))
		{
			# Cache hit :)
			if (Application::$INSTANCE->isJSON())
			{
				return GDT_JSON::make()->value($content);
			}
			return GDT_HTML::make()->var($content);
		}
		else
		{
			$app = Application::$INSTANCE;
			$result = $this->execute();
			if (!$app->isError())
			{
				$content = $result->renderMode(Application::$MODE_DETECTED);
				Cache::fileSetSerialized($key, $content);
				if (is_array($content))
				{
					return GDT_JSON::make()->value($content);
				}
				else
				{
					return GDT_HTML::make()->value($content);
				}
			}
			return $result;
		}
	}
	
}
