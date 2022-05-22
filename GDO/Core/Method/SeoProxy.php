<?php
namespace GDO\Core\Method;

use GDO\Core\Method;

/**
 * Proxy an http request / url to a method via GDOv7 SEO url rules.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 * @see Method
 */
final class SeoProxy extends Method
{
	public function execute()
	{
		$url = $_REQUEST['url'];
		
	}
	
}
