<?php
namespace GDO\User;

use GDO\UI\GDT_Link;
use GDO\Core\GDT_Template;

/**
 * A link to a profile.
 * 
 *  - Requires user()
 *  
 *  - Optional level()
 *  - Optional avatar()
 *  - Optional nickname()
 *  
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
final class GDT_ProfileLink extends GDT_Link
{
	use WithUser;
	use WithAvatar;
	
	public string $icon = 'user';
	
	public function hrefProfile() : string
	{
		return $this->user->href_profile();
	}
	
	################
	### Nickname ###
	################
	public bool $nickname = false;
	public function nickname(bool $nickname = true) : self
	{
		$this->nickname = $nickname;
		return $this;
	}
	
	#############
	### Level ###
	#############
	public bool $level = false;
	public function level(bool $level=true) : self
	{
		$this->level = $level;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		$tVars = [
			'field' => $this,
			'user' => isset($this->user) ? $this->user : GDO_User::current(),
		];
		return GDT_Template::php('User', 'profile_link_html.php', $tVars);
	}
	
}
