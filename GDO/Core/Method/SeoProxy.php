<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\ModuleLoader;

/**
 * Proxy an HTTP request / URL to a Method via GDOv7 SEO url rules.
 * Is not trivial, means it does not get run in automated tests.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 * @see Method
 */
final class SeoProxy extends Method
{
	/**
	 * Makes no sense to test this in the usual stack, as it builds method params. @TODO Write a test for 403, 404 and SEOProxy.
	 */
	public function isTrivial() : bool { return false; }

	/**
	 * Create a method with parameters from a GDOv7 SEO URL.
	 */
	public static function makeProxied(string $url) : Method
	{
		$loader = ModuleLoader::instance();
		$args = explode('/', trim($url, '/ '));

		# Module
		$mo = array_shift($args);
		$module = $loader->getModule($mo, false, false);
		if (!$module)
		{
			$_REQUEST['url'] = $url; # and a step back for 404 url :)
			return FileNotFound::make();
		}
		
		# Method
		if (!($me = array_shift($args)))
		{
			$_REQUEST['url'] = $url; # and a step back for 404 url :)
			return FileNotFound::make();
		}
		
		if (!($method = $module->getMethodByName($me, false)))
		{
			$_REQUEST['url'] = $url; # and a step back for 404 url :)
			return FileNotFound::make();
		}
		
		# Parameters
		$i = 0;
		$len = count($args);
		while ($i < $len)
		{
			$key = $args[$i++];
			if (!isset($_REQUEST[$key]))
			{
				$_REQUEST[$key] = $args[$i];
			}
			$i++;
		}
		
		return $method;
	}
		
	public function execute()
	{
		$method = self::makeProxied($_REQUEST['url']);
		return $method->exec();
	}
	
}
