<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDO_Exception;
use GDO\Core\GDT;
use GDO\Core\WithGDO;
use GDO\DB\Query;
use GDO\UI\GDT_Container;
use GDO\UI\WithIcon;
use GDO\UI\WithLabel;

/**
 * An ACL adds 3 fields to a GDT.
 *
 * 1) GDT_ACLRelation - friend, member, friends friend etc
 * 2) GDT_Level       - the level required
 * 3) GDT_Permission  - the group required
 *
 * This is used in profiles, user config and settings.
 *
 * The site config allows to toggle which of the 3 fields are used, in all combinations.
 *
 * @version 7.0.3
 * @since 6.4.0
 * @author gizmore
 * @see Module_Account
 * @see Module_User
 */
final class GDT_ACL extends GDT
{

	use WithGDO;
	use WithIcon;
	use WithLabel;

	public GDT_ACLRelation $aclRelation;

	public GDT_Level $aclLevel;

	public GDT_Permission $aclPermission;

// 	public function isTestable(): bool { return false; }

	private bool $aclcapable = false;
	public bool $withPermission = true;

//	public string $initial = '';

	public static function make(string $name = null): static
	{
		return static::makeNamed($name)->initACLFields()->label($name);
	}

	private function initACLFields(): static
	{
		$this->aclRelation = GDT_ACLRelation::make("{$this->name}_relation")->noacl();
		$this->aclLevel = GDT_Level::make("{$this->name}_level")->noacl();
		$this->aclPermission = GDT_Permission::make("{$this->name}_permission")->onlyPermitted()->noacl();
		return $this;
	}

	public function noacl(): static
	{
		return $this->aclcapable(false);
	}

	###########
	### ACL ###
	###########

	public function aclcapable(bool $aclcapable = true): static
	{
		$this->aclcapable = $aclcapable;
		return $this;
	}

	public function isACLCapable(): bool
	{
		return $this->aclcapable;
	}

	public function isWriteable(): bool
	{
		return true;
	}

	public function getGDOData(): array
	{
		return array_merge(
			$this->aclRelation->getGDOData(),
			$this->aclLevel->getGDOData(),
			$this->aclPermission->getGDOData(),
		);
	}

	public function setGDOData(array $data): static
	{
		$this->aclRelation->setGDOData($data);
		$this->aclLevel->setGDOData($data);
		$this->aclPermission->setGDOData($data);
		return $this;
	}

	############
	### Data ###
	############

	public function hasChanged(): bool
	{
		return
			$this->aclRelation->hasChanged() ||
			$this->aclLevel->hasChanged() ||
			$this->aclPermission->hasChanged();
	}

	public function inputs(?array $inputs): static
	{
		$this->aclRelation->inputs($inputs);
		$this->aclLevel->inputs($inputs);
		$this->aclPermission->inputs($inputs);
		return $this;
	}

	public function gdoColumnDefine(): string
	{
		return
			$this->aclRelation->gdoColumnDefine() . ",\n" .
			$this->aclLevel->gdoColumnDefine() . ",\n" .
			$this->aclPermission->gdoColumnDefine();
	}

	public function renderHTML(): string
	{
//		$this->setupOwnLabels();
		return GDT_Container::makeWith($this->aclRelation, $this->aclLevel, $this->aclPermission)->horizontal()->renderHTML();
	}

//	private function setupOwnLabels(): void
//	{
//		$label = $this->renderLabel();
//		$this->aclRelation->label('lbl_own_acl_relation', [$label]);
//		$this->aclLevel->label('lbl_own_acl_level', [$label]);
//		$this->aclPermission->label('lbl_own_acl_permission', [$label]);
//	}

	##########
	### DB ###
	##########

	public function initialACL(string $relation, string $level = null, string $permission = null): static
	{
		$this->aclRelation->initial($relation);
		$this->aclLevel->initial($level);
		$this->aclPermission->initial($permission);
		return $this;
	}

	###########
	### API ###
	###########

	public function setupLabels(GDT $gdt): void
	{
        $label = $gdt->renderLabel();
        $this->aclLevel->label('lbl_acl_level', [$label]);
		$this->aclRelation->label('lbl_acl_relation', [$label]);
		$this->aclPermission->label('lbl_acl_permission', [$label]);
	}

	public function noPermission():	self
	{
		return $this->withPermission(false);
	}


	##############
	### Render ###
	##############

	public function withPermission(bool $withPermission = true): static
	{
		$this->withPermission = $withPermission;
		return $this;
	}

	###############
	### Private ###
	###############

	public function hasAccess(GDO_User $user, GDO_User $target, string &$reason): bool
	{
		$mu = Module_User::instance();

		# Self is fine
		if ($user === $target)
		{
			return true;
		}

		# Staff may see everything
		if ($user->isStaff())
		{
			return true;
		}

		# Check relation
		if ($mu->cfgACLRelations())
		{
			if (!$this->aclRelation->hasAccess($user, $target, $reason))
			{
				return false;
			}
		}

		# Check level
		if ($mu->cfgACLLevels())
		{
			$minLevel = $this->aclLevel->getValue();
			$userLevel = $user->getLevel();
			if ($userLevel < $minLevel)
			{
				$reason = t('err_only_level_access', [$minLevel]);
				return false;
			}
		}

		# Check permission
		if ($mu->cfgACLPermissions())
		{
			if ($permission = $this->aclPermission->getValue())
			{
				if (!$user->hasPermissionObject($permission))
				{
					$reason = t('err_only_permission_access', [$permission->renderName()]);
					return false;
				}
			}
		}

		return true;
	}

	public function queryWhereVisible(Query $query, string $moduleName, string $key, GDO_User $user): static
	{
// 		$module = ModuleLoader::instance()->getModule($moduleName);
// 		$gdt = $module->setting($key);
// 		$acl = $module->getSettingACL($key);

// 		if ($user->isStaff())
// 		{
// 			return $this;
// 		}

		# 3 Conditions for each access type. rel,lvl,perm
		$conditions = [];
		$conditions[] = "uset_level<={$user->getLevel()}";
// 		$conditions[] = " ( SELECT 1 FROM gdo_userpermission WHERE perm_perm_id=uset_permission AND perm_user_id={$user->getID()})";

		$rels = ['acl_all'];
		if ($user->isMember())
		{
			$rels[] = 'acl_members';
		}
		if ($user->isUser())
		{
			$rels[] = 'acl_guests';
		}
// 		if (module_enabled('Friends'))
// 		{
// 			$rels[] = " (SELECT 1 FRO;) ";
// 		}
		$rels = "'" . implode("', '", $rels) . "'";
		$conditions[] = 'uset_relation IN (' . $rels . ')';
		$conditions = implode(' AND ', $conditions);

// 		$query->where("uset_name=" . quote($key));
		$and = "(uset_user={$user->getID()}) OR ($conditions)";
		$query->where($and);
//
// $q  = $query->buildQuery();


// 		switch ($rel)
// 		{
// 			case GDT_ACLRelation::NOONE:
// 			case GDT_ACLRelation::HIDDEN:
// 				$condition[] = '0';
// 			case GDT_ACLRelation::FRIENDS:
// 		}
// 		$condition[] =  "uset_relation IN ({$rel}) OR ";
// 		$query->orWhere(implode(' AND ', $condition));
		return $this;
	}

}
