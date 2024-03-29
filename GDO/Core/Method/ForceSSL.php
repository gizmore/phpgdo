<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Redirect;

/**
 * Force the TLS Protocol on all pages.
 *
 * @since 7.0.3
 * @author gizmore
 */
final class ForceSSL extends Method
{

	public function isHiddenMethod(): bool
	{
		return true;
	}

	public function execute(): GDT
	{
		$url = $_SERVER['REQUEST_URI'];
		$url = GDT_Link::absolute($url, true);
		return GDT_Redirect::make()->href($url)->text('err_force_ssl');
	}

}
