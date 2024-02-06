<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDT_Template;
use GDO\Core\WithGDO;
use GDO\UI\GDT_Link;
use GDO\UI\TextStyle;

/**
 * A link to a profile.
 * If the user is admin, he is rel="author".
 *
 *  - Requires user()
 *
 *  - Optional level()
 *  - Optional avatar()
 *  - Optional nickname()
 *
 * @version 7.0.2
 * @since 6.2.0
 * @author gizmore
 */
final class GDT_ProfileLink extends GDT_Link
{

	use WithGDO;
	use WithAvatar;

	public string $icon = 'user';
	public bool $nickname = false;
	public bool $avatar = false;

	################
	### Nickname ###
	################
	public bool $level = false;

	public function hrefProfile(): string
	{
		return $this->getGDO()->hrefProfile();
	}

    public function gdtDefaultLabel(): ?string
    {
        return 'user_name';
    }

    ##############
	### Avatar ###
	##############

	public function user(GDO $gdo = null): self
	{
		return $this->gdo($gdo);
	}

	public function nickname(bool $nickname = true): self
	{
		$this->nickname = $nickname;
		return $this;
	}

	#############
	### Level ###
	#############

	public function avatar(bool $avatar = true): self
	{
		$this->avatar = $avatar && module_enabled('Avatar');
		return $this;
	}

	public function level(bool $level = true): self
	{
		$this->level = $level;
		return $this;
	}

    /**
     * @throws GDO_DBException
     */
    public function username(string $username): self
    {
        $user = GDO_User::getById($username);
        return $this->gdo($user);
    }

    ##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		if ($this->avatar)
		{
			$this->avatarUser($this->getGDO(), $this->avatarSize);
		}
		$tVars = [
			'field' => $this,
			'user' => $this->getGDO(),
		];
		return GDT_Template::php('User', 'profile_link_html.php', $tVars);
	}

	public function renderCLI(): string
	{
		return isset($this->gdo) ?
			$this->gdo->renderUserName() :
			TextStyle::italic(t('unknown_user'));
	}

}
