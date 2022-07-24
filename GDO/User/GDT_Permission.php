<?php
namespace GDO\User;

use GDO\Core\GDT_ObjectSelect;

/**
 * Permission select.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
final class GDT_Permission extends GDT_ObjectSelect
{
	public function defaultLabel() : self { return $this->label('permission'); }
	
	protected function __construct()
	{
	    parent::__construct();
		$this->table(GDO_Permission::table());
// 		$this->emptyLabel('none');
	}
	
	#################
	### Permitted ###
	#################
	public $onlyPermitted = false;
	public function onlyPermitted(bool $onlyPermitted=true) : self
	{
		$this->onlyPermitted = $onlyPermitted;
		return $this;
	}

	##############
	### Select ###
	##############
	public function getChoices()
	{
		$choices = parent::getChoices();
		if ($this->onlyPermitted)
		{
			$choices = array_filter($choices, function(GDO_Permission $permission) {
				return GDO_User::current()->hasPermissionObject($permission);
			});
		}
		return $choices;
	}
	
}
