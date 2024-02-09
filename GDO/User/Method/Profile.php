<?php
namespace GDO\User\Method;

use GDO\Avatar\GDO_Avatar;
use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\Core\Website;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Tooltip;
use GDO\UI\MethodCard;
use GDO\User\GDO_Profile;
use GDO\User\GDO_User;
use GDO\User\Module_User;
use Ratchet\App;

/**
 * Show a user's profile.
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
final class Profile extends MethodCard
{

	public function idName(): string { return 'for'; }

	public function gdoTable(): GDO
	{
		return GDO_User::table();
	}

	public function getMethodTitle(): string
	{
		$user = $this->getUser();
		return t('mt_user_profile', [$user->renderUserName()]);
	}

	public function getUser(): GDO_User
	{
		if (!($user = $this->getObject()))
		{
			$user = GDO_User::current();
		}
		return $user;
	}

	public function getMethodDescription(): string
	{
		$user = $this->getUser();
		return t('md_user_profile', [
			$user->renderUserName(),
			sitename(),
			$this->getCLIOutput(),
		]);
//		return t('md_user_profile');
	}

	private function getCLIOutput(): string
	{
        $old = Application::$MODE;
        Application::$MODE = GDT::RENDER_TEXT;
		$profile = GDO_Profile::forUser($this->getUser());
		$card = $this->getCard($profile);
		$back = $card->render();
        Application::$MODE = $old;
        return $back;
	}

	public function afterExecute(): void
	{
		if (module_enabled('Avatar'))
		{
			$this->addAvatarImageToMeta();
		}
	}

	private function addAvatarImageToMeta(): void
	{
		$user = $this->getUser();
		$avatar = GDO_Avatar::forUser($user);
		Website::addMeta(['og:image', $avatar->hrefImage(), 'property']);
	}

	public function execute(): GDT
	{
		$user = $this->getUser();
		if (!$user)
		{
			return $this->error('err_no_data_yet');
		}

		$me = GDO_User::current();
		if ($user === $me)
		{
			if (module_enabled('Account'))
			{
				$info = GDT_Panel::make()->text('p_info_own_profile', [
					GDT_Link::make()->text('link_account')->href(href('Account', 'AllSettings'))->render(),
				]);
				GDT_Page::instance()->topResponse()->addField($info);
			}
		}

		$this->onProfileView($user);
		$profile = GDO_Profile::forUser($user);
		return $this->executeFor($profile);
	}

	public function onProfileView(GDO_User $user): void
	{
		Module_User::instance()->increaseUserSetting($user, 'profile_views');
	}

	public function createCard(GDT_Card $card): void
	{
		/** @var GDO_User $user * */
		$user = $card->gdo->getUser();
//		$user = $card->gdo->getUser();
		$card->creatorHeader('profile_user', 'profile_activity');
		$card->titleRaw(t('mt_user_profile', [$user->renderUserName()]));
		$modules = ModuleLoader::instance()->getEnabledModules();
		$card->subtitleRaw(t('profile_level', [
			self::getHighestPermission($user),
			$user->getLevel()]));
		foreach ($modules as $module)
		{
			$this->createCardB($card, $module);
		}
	}

	/**
	 * Get the highest permission name/title for user.
	 */
	public static function getHighestPermission(GDO_User $user): string
	{
		if ($perms = $user->loadPermissions())
		{
			$perm = array_shift($perms);
			return t("perm_{$perm}");
		}
		elseif ($user->isMember())
		{
			return t('member');
		}
		elseif ($user->isGuest())
		{
			return t('guest');
		}
		else
		{
			return t('ghost');
		}
	}

	private function createCardB(GDT_Card $card, GDO_Module $module): void
	{
		$user = GDO_User::current();
		$target = $this->getUser();
		$cache = $module->getSettingsCache();
		foreach ($cache as $gdt)
		{
			$name = $gdt->getName();
			if (!$gdt->isSerializable())
			{
				continue; # skip passwords.
			}
			if (!$gdt->isACLCapable())
			{
				continue; # skip fields that are not meant to be shown.
			}
			if (!($acl = $module->getUserConfigACLField($name)))
			{
				continue; # skip fields that are not meant to be shown.
			}
			$gdt = $module->userSetting($target, $name);
			$reason = '';
//			if ($gdt->var)
//			{
				if (!$acl->hasAccess($user, $target, $reason))
				{
					if ($reason)
					{
						$card->addField(GDT_Tooltip::make()->labelRaw($gdt->renderLabel())->tooltipRaw($reason));
					}
				}
				else
				{
					$card->addField($gdt);
				}
//			}
		}
	}

}
