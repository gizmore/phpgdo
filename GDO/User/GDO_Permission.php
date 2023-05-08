<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_UInt;
use GDO\Core\GDT_Virtual;
use GDO\Language\Trans;
use GDO\UI\GDT_EditButton;

/**
 * Permission entities.
 * One of the first GDO that use GDT_Virtual, for usercount.
 *
 * @version 7.0.3
 * @since 3.1.0
 * @author gizmore
 * @see GDT_Virtual
 */
final class GDO_Permission extends GDO
{

	final public const CRONJOB = 'cronjob';
	final public const STAFF = 'staff';
	final public const ADMIN = 'admin';

	public static function create(string $name): self
	{
		return self::getByName($name) ?: self::blank(['perm_name' => $name])->insert();
	}

	public static function getByName(string $name): ?self
	{
		return self::getBy('perm_name', $name);
	}

	##############
	### Getter ###
	##############

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('perm_id'),
			GDT_Name::make('perm_name')->notNull(),
			GDT_Virtual::make('perm_usercount')->gdtType(GDT_UInt::make())->label('user_count')->
			subquery('SELECT COUNT(*) FROM gdo_userpermission WHERE perm_perm_id = perm_id'),
		];
	}

	public function display_perm_edit(): string { return GDT_EditButton::make()->href($this->href_edit())->render(); }

	public function display_user_count(): string { return $this->gdoVar('user_count'); }

	public function getName(): ?string { return $this->gdoVar('perm_name'); }

	public function href_edit(): string
	{
		return href('Admin', 'ViewPermission', '&permission=' . $this->getID());
	}

	##############
	### Render ###
	##############


	public function renderName(): string
	{
		$name = $this->getName();
		$key = 'perm_' . $name;
		if (Trans::hasKey($key))
		{
			return t($key);
		}
		return $name ?: t('unknown_permission');
	}


	public function renderOption(): string
	{
		return sprintf('%s–%s', $this->getID(), $this->renderName());
	}

}
