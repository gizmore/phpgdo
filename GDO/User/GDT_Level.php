<?php
namespace GDO\User;

use GDO\Core\WithGDO;
use GDO\UI\GDT_Badge;

/**
 * User level field.
 *
 * If the gdo is a user, it reads combined level of user permission.
 * NotNull, initial 0, because we want to do arithmetics.
 * With trophy icon.
 * Renders effective level in table cells.
 *
 * @version 7.0.1
 * @since 6.0.2
 * @author gizmore
 */
final class GDT_Level extends GDT_Badge
{

	use WithGDO;

	protected function __construct()
	{
		parent::__construct();
		$this->writeable(true);
		$this->icon('level');
	}

	public function defaultLabel(): self
	{
		return $this->label('user_level');
	}

	public function isSearchable(): bool { return false; }

	#################
	### Var / Val ###
	#################

	public function getVar(): string|array|null
	{
		if (isset($this->gdo))
		{
			if (!$this->gdo->gdoIsTable())
			{
				if ($this->gdo instanceof GDO_User)
				{
					return $this->gdo->getLevel();
				}
			}
		}
		return parent::getVar();
	}

}
