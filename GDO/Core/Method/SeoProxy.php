<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\Application;
use GDO\Core\GDO_Exception;
use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;

/**
 * Proxy an HTTP request / URL to a Method via GDOv7 SEO url rules.
 * Is not trivial, means it does not get run in automated tests.
 *
 * @version 7.0.3
 * @since 7.0.1
 * @author gizmore
 * @see Method
 */
final class SeoProxy extends Method
{

	/**
	 * Makes no sense to test this in the usual stack, as it builds method params.
	 * @TODO Write a test for 403, 404 and SEOProxy.
	 */
	public function isTrivial(): bool { return false; }

    /**
     * @throws GDO_Exception
     */
    public function execute(): GDT
	{
		$method = self::makeProxied($_REQUEST['_url']);
		return $method->exec();
	}

    /**
     * Create a method with parameters from a GDOv7 SEO URL.
     * @throws GDO_Exception
     */
	public static function makeProxied(string $url): Method
	{
		$loader = ModuleLoader::instance();
		$path = trim($url, '/');
		# The extension is not an argument. Remove it before splitting dotted
		# routes, otherwise `.html` looks like a parameter without a value.
		$suffix = pathinfo($path, PATHINFO_EXTENSION) ?: 'html';
		$path = preg_replace('/\\.[A-Za-z0-9]+$/', '', $path);
		$args = explode(GDO_SEO_SEP, trim($path, GDO_SEO_SEP));

		$app = Application::$INSTANCE;
		$app->modeDetected($app->detectRenderMode($suffix));

		# Module
		$mo = array_shift($args);
		$module = $loader->getModule($mo, false, false);
		if (!$module)
		{
			$_REQUEST['_url'] = $url; # and a step back for 404 url :)
			return FileNotFound::make();
		}

		# Method
		if (!($me = array_shift($args)))
		{
			$_REQUEST['_url'] = $url; # and a step back for 404 url :)
			return FileNotFound::make();
		}

		if (!($method = $module->getMethodByName($me, false)))
		{
			$_REQUEST['_url'] = $url; # and a step back for 404 url :)
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
				$_REQUEST[$key] = @$args[$i];
			}
			$i++;
		}

		return $method;
	}

}
