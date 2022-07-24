<?php
namespace GDO\User;

use GDO\Core\WithName;
use GDO\Core\WithGDO;
use GDO\Core\GDT;
use GDO\UI\GDT_Container;
use GDO\UI\WithLabel;

/**
 * An ACL adds 3 fields to a GDT.
 * 
 * 1) GDT_ACLRelation - friend, member, friends friend etc
 * 2) GDT_Permission - the group required
 * 3) GDT_Level - the level required
 * 
 * This is used in profiles / user config and settings.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 * 
 * @see Module_Account
 * @see Module_User
 */
final class GDT_ACL extends GDT
{
	use WithGDO;
	use WithName;
	use WithLabel;
	
	public GDT_Level $aclLevel;
	public GDT_ACLRelation $aclRelation;
	public GDT_Permission $aclPermission;
	
	public function isACLCapable() : bool { return $this->aclcapable; }
	public bool $aclcapable = false;
	public function noacl() : self { return $this->aclcapable(false); }
	public function aclcapable(bool $aclcapable=true) : self
	{
		$this->aclcapable = $aclcapable;
		return $this;
	}
	
	public static function make(string $name=null) : self
	{
		$obj = self::makeNamed($name);
		$obj->initACLFields();
		return $obj;
	}
	
	###########
	### ACL ###
	###########
	public function initialACL(string $relation, int $level=0, string $permission=null) : self
	{
		$this->aclLevel->initial($level);
		$this->aclRelation->initial($relation);
		$this->aclPermission->initial($permission);
		return $this;
	}
	
	public function setupLabels(GDT $gdt)
	{
		$label = $gdt->renderLabel();
		$this->aclLevel->label('lbl_acl_level', [$label]);
		$this->aclRelation->label('lbl_acl_relation', [$label]);
		$this->aclPermission->label('lbl_acl_permission', [$label]);
	}
	
	public bool $withPermission = true;
	public function noPermission() : self { return $this->withPermission(false); }
	public function withPermission(bool $withPermission=true) : self
	{
		$this->withPermission = $withPermission;
		return $this;
	}
	
	############
	### Data ###
	############
	public function getGDOData() : ?array
	{
		return array_merge(
			$this->aclLevel->getGDOData(),
			$this->aclRelation->getGDOData(),
			$this->aclPermission->getGDOData(),
		);
	}
	
	public function setGDOData(array $data) : self
	{
		$this->aclLevel->setGDOData($data);
		$this->aclRelation->setGDOData($data);
		$this->aclPermission->setGDOData($data);
		return $this;
	}
	
	##########
	### DB ###
	##########
	public function gdoColumnDefine() : string
	{
		return
			$this->aclLevel->gdoColumnDefine() . "\n" .
			$this->aclRelation->gdoColumnDefine() . "\n" .
			$this->aclPermission->gdoColumnDefine();
	}
	
	###########
	### API ###
	###########
	public function hasAccess(GDO_User $user, GDO_User $target, string &$reason) : bool
	{
		# Self is fine
		if ($user === $target)
		{
			return true;
		}
		
		# Check level
		$minLevel = $this->aclLevel->getValue();
		$userLevel = $user->getLevel();
		if ($userLevel < $minLevel)
		{
			$reason = t('err_only_level_access', [$minLevel]);
			return false;
		}
		
		# Check relation
		if (!$this->aclRelation->hasAccess($user, $target, $reason))
		{
			return false;
		}
		
		# Check permission
		$permission = $this->aclPermission->getValue();
		if (!$user->hasPermissionObject($permission))
		{
			$reason = t('err_only_permission_access', [$permission->renderName()]);
			return false;
		}
		
		# This is fine. Ô-o
		return true;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		$this->setupOwnLabels();
		return GDT_Container::makeWith($this->aclRelation, $this->aclLevel, $this->aclPermission)->horizontal()->render();
	}
	
	###############
	### Private ###
	###############
	private function initACLFields() : void
	{
		$this->aclLevel = GDT_Level::make("{$this->name}_level");
		$this->aclRelation = GDT_ACLRelation::make("{$this->name}_relation");
		$this->aclPermission = GDT_Permission::make("{$this->name}_permission")->onlyPermitted();
	}

	private function setupOwnLabels()
	{
		$label = $this->renderLabel();
		$this->aclLevel->label('lbl_own_acl_level', [$label]);
		$this->aclRelation->label('lbl_own_acl_relation', [$label]);
		$this->aclPermission->label('lbl_own_acl_permission', [$label]);
	}
	
}