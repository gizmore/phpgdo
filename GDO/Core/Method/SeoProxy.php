<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\GDO_StubException;
use GDO\Net\GDT_Url;

/**
 * Proxy an http request / url to a method via GDOv7 SEO url rules.
 * Is not trivial. Means it does not get run in automated test automagically.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 * @see Method
 */
final class SeoProxy extends Method
{
	public function isTrivial() : bool { return false; }
	
	public function execute()
	{
		$url = $_REQUEST['url'];
		throw new GDO_StubException($this->gdoClassName());
	}
	
}
