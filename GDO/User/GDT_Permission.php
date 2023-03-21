<?php
namespace GDO\User;

use GDO\Core\GDT_ObjectSelect;

/**
 * Permission select.
 *
 * @version 7.0.1
 * @since 6.1.0
 * @author gizmore
 */
final class GDT_Permission extends GDT_ObjectSelect
{

	public $onlyPermitted = false;

	protected function __construct()
	{
		parent::__construct();
		$this->icon('medal');
		$this->table(GDO_Permission::table());
		$this->emptyLabel('none');
	}

	#################
	### Permitted ###
	#################

	public function defaultLabel(): self { return $this->label('permission'); }

	public function getChoices(): array
	{
		$choices = parent::getChoices();
		if ($this->onlyPermitted)
		{
			$choices = array_filter($choices, function (GDO_Permission $permission)
			{
				return GDO_User::current()->hasPermissionObject($permission);
			});
		}
		return $choices;
	}

	##############
	### Select ###
	##############

	public function onlyPermitted(bool $onlyPermitted = true): self
	{
		$this->onlyPermitted = $onlyPermitted;
		return $this;
	}

}
