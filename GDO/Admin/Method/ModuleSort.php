<?php
namespace GDO\Admin\Method;

use GDO\Core\Method;
use GDO\Core\GDO_Module;
use GDO\DB\Cache;
use GDO\Table\MethodSort;

/**
 * Drag and drop sorting of modules.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
final class ModuleSort extends MethodSort
{
	/**
	 * Only staff may sort modules for navbar appearance.
	 * {@inheritDoc}
	 * @see Method::getPermission()
	 */
	public function getPermission() : ?string { return 'staff'; }

	public function gdoSortObjects() { return GDO_Module::table(); }

	public function afterExecute() : void
	{
		Cache::remove('gdo_modules');
	}

}
