<?php
namespace GDO\User\Method;

use GDO\Core\Method;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;

/**
 * Print your username profile link.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.4
 */
final class WhoAmI extends Method
{
	
	public function execute()
	{
		return GDT_ProfileLink::make('user')->user(GDO_User::current())->nickname();
	}
	
}
