<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\ModuleLoader;

/**
 * Proxy an http request / url to a method via GDOv7 SEO url rules.
 * Is not trivial. Means it does not get run in automated test automagically.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 * @see Method
 */
final class SeoProxy extends Method
{
	public function isTrivial() : bool { return false; }
	
	public static function makeProxied(string $url) : Method
	{
		$loader = ModuleLoader::instance();
		$args = explode('/', trim($url, '/ '));
		$mo = array_shift($args);
		$me = array_shift($args);
		$module = $loader->getModule($mo);
		$method = $module->getMethodByName($me);
		while (count($args) >= 2)
		{
			$_REQUEST[array_shift($args)] = array_shift($args);
		}
		return $method;
	}
		
	public function execute()
	{
		$method = self::makeProxied($_REQUEST['url']);
		return $method->exec();
	}
	
}
