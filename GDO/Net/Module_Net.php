<?php
namespace GDO\Net;

use GDO\Core\GDO_Module;

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
			GDO_Domain::class,
			GDO_SubDomain::class,
		];
	}
	
}
