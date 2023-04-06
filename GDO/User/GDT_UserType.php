<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDT_Enum;

/**
 * User type enum.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 */
final class GDT_UserType extends GDT_Enum
{

	final public const SYSTEM = 'system';
	final public const GHOST = 'ghost';
	final public const GUEST = 'guest';
	final public const MEMBER = 'member';
	final public const BOT = 'bot'; # @TODO: make use of new user type "bot"
	final public const LINK = 'link'; # @TODO: make use of new user type "link"

	protected function __construct()
	{
		parent::__construct();
		$this->enumValues(
			self::SYSTEM, self::GHOST, self::GUEST,
			self::MEMBER, self::BOT, self::LINK);
		$this->initial(self::GHOST);
	}

}
