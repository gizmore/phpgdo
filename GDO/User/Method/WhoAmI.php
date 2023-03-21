<?php
namespace GDO\User\Method;

use GDO\Core\Method;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;

/**
 * Print your username profile link.
 *
 * @version 7.0.1
 * @since 6.10.4
 * @author gizmore
 */
final class WhoAmI extends Method
{

	public function isShownInSitemap(): bool
	{
		return false;
	}

	public function getMethodTitle(): string
	{
		return t('gdo_user');
	}

	public function execute()
	{
		return GDT_ProfileLink::make('user')->user(GDO_User::current())->nickname();
	}

}
