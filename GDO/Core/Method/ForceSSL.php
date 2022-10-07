<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\UI\GDT_Redirect;
use GDO\UI\GDT_Link;

/**
 * Force the TLS Protocol on all pages.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class ForceSSL extends Method
{
	public function execute()
	{
		$url = $_SERVER['REQUEST_URI'];
		$url = GDT_Link::absolute($url, true);
		return GDT_Redirect::make()->href($url)->text('err_force_ssl');
	}
	
}
