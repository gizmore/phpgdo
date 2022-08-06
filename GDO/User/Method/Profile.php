<?php
namespace GDO\User\Method;

use GDO\Core\GDO;
use GDO\UI\MethodCard;
use GDO\User\GDO_User;
use GDO\User\GDO_Profile;
use GDO\UI\GDT_Card;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO_Module;
use GDO\User\Module_User;
use GDO\UI\GDT_Tooltip;

/**
 * Show a user's profile.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Profile extends MethodCard
{
	public function gdoTable(): GDO
	{
		return GDO_User::table();
	}
	
	public function getMethodTitle() : string
	{
		return t('mt_user_profile', [$this->getObject()->renderUserName()]);
	}
	
	public function execute()
	{
		$user = $this->getObject();
		if (!$user)
		{
			return $this->error('err_no_data_yet');
		}
		$profile = GDO_Profile::forUser($user);
		Module_User::instance()->increaseUserSetting($user, 'profile_views');
		return $this->executeFor($profile);
	}

	protected function createCard(GDT_Card $card) : void
	{
		$card->creatorHeader('profile_user', 'profile_created');
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$this->createCardB($card, $module);
		}
	}
	
	private function createCardB(GDT_Card $card, GDO_Module $module) : void
	{
		$user = GDO_User::current();
		$target = $this->getObject();
		foreach ($module->getSettingsCache() as $gdt)
		{
			$name = $gdt->getName();
			if (!$gdt->isACLCapable())
			{
				continue; # skip fields that are not meant to be shown.
			}
			if (!($acl = $module->getUserConfigACLField($name)))
			{
				continue; # skip fields that are not meant to be shown.
			}
			$gdt = $module->userSetting($target, $gdt->getName());
			$reason = '';
			if (!$acl->hasAccess($user, $target, $reason))
			{
				$card->addField(GDT_Tooltip::make()->labelRaw($gdt->renderLabel())->tooltipRaw($reason));
			}
			else
			{
				$card->addField($gdt);
			}
		}
	}
	
}
