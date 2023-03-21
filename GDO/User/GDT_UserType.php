<?php
namespace GDO\User;

use GDO\Core\GDT_Enum;

/**
 * User type enum.
 *
 * @version 7.0.0
 * @since 7.0.0
 * @author gizmore
 */
final class GDT_UserType extends GDT_Enum
{

	public const SYSTEM = 'system';
	public const GHOST = 'ghost';
	public const GUEST = 'guest';
	public const MEMBER = 'member';
	public const BOT = 'bot'; # @TODO: make use of new user type "bot"
	public const LINK = 'link'; # @TODO: make use of new user type "link"

	protected function __construct()
	{
		parent::__construct();
		$this->enumValues(
			self::SYSTEM, self::GHOST, self::GUEST,
			self::MEMBER, self::BOT, self::LINK);
		$this->initial(self::GHOST);
		$this->notNull();
	}

}
