<?php
namespace GDO\User\Method;

use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Admin\MethodAdmin;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\Core\GDT;

final class Admin extends Method
{
	use MethodAdmin;
	
	public function execute() : GDT
	{
		# Admin menu
		$menu = GDT_Bar::make()->horizontal();
		$menu->addField(GDT_Link::make('link_user_add_user')->href(href('User', 'AddUser')));
		$response = GDT_Response::makeWith($menu);
		
		# Tabs first
		return $response;
	}

}
