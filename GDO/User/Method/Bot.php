<?php
declare(strict_types=1);
namespace GDO\User\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Checkbox;
use GDO\Core\Method;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/** Toggle the visual bot marker for an account. */
final class Bot extends Method
{

	public function isCLI(): bool { return true; }

	public function getUserType(): ?string { return 'member'; }

	public function gdoParameters(): array
	{
		return [
			GDT_User::make('user')->fallbackCurrentUser(),
			GDT_Checkbox::make('bot')->notNull(),
		];
	}

	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		$target = $this->gdoParameterValue('user') ?: $user;
		if ($target->getID() === $user->getID() || $user->isStaff())
		{
			return true;
		}
		$error = 'err_permission_required';
		$args = [];
		return false;
	}

	public function execute(): GDT
	{
		$target = $this->gdoParameterValue('user') ?: GDO_User::current();
		$enabled = (bool)$this->gdoParameterValue('bot');
		$target->saveSettingVar('User', 'bot', $enabled ? '1' : '0');
		return $this->message('msg_user_bot', [
			$target->renderUserName(),
			$enabled ? t('enum_yes') : t('enum_no'),
		]);
	}

}
