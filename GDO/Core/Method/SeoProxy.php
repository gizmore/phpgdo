<?php
namespace GDO\Core\Method;

use GDO\Core\Method;

/**
 * Proxy the call via SEO url rules.
 * @author gizmore
 */
final class SeoProxy extends Method
{
	public function execute()
	{
		$url = $_REQUEST['url'];
		
	}
	
}
