<?php
namespace GDO\Net;

use GDO\Core\GDO_Module;
use GDO\Core\GDO_SEO_URL;

/**
 * Network related stuff.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.11.0
 */
final class Module_Net extends GDO_Module
{
	public int $priority = 9;
	
	public function onLoadLanguage() : void
	{
		$this->loadLanguage('lang/net');
	}
	
	public function getClasses() : array
	{
		return [
			GDO_SEO_URL::class,
			GDO_Domain::class,
			GDO_SubDomain::class,
		];
	}
	
	public function OnInstall() : void
	{
		if (GDO_SEO_URLS)
		{
			GDO_SEO_URL::addRoute('robots.txt', 'index.php?_mo=Core&_me=Robots');
			GDO_SEO_URL::addRoute('SECURITY.md', 'index.php?_mo=Core&_me=Security');
		}
	}
	
}
