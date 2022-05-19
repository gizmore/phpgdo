<?php
namespace GDO\User;

use GDO\Core\GDT_Enum;

/**
 * User type enum.
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
final class GDT_UserType extends GDT_Enum
{
	const SYSTEM = 'system';
	const GHOST = 'ghost';
	const GUEST = 'guest';
	const MEMBER = 'member';
	const LINK = 'link'; # @TODO: make use of new user type "link"
	
	protected function __construct()
	{
		parent::__construct();
		$this->enumValues(self::SYSTEM, self::GHOST, self::GUEST, self::MEMBER, self::LINK);
		$this->initial(self::GHOST);
		$this->notNull();
	}
	
}
