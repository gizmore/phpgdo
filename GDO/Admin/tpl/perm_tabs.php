<?php
namespace GDO\Admin\tpl;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Box;
echo GDT_Box::make()->addFields(
	GDT_Link::make('link_add_perm')->href(href('Admin', 'PermissionAdd'))->icon('create'),
    GDT_Link::make('link_grant_perm')->href(href('Admin', 'PermissionGrant'))->icon('add'),
    GDT_Link::make('link_revoke_perm')->href(href('Admin', 'PermissionRevoke'))->icon('remove')
)->horizontal()->render();
