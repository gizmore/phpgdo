<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDT_Enum;

/**
 * Gender enum.
 *
 * @version 7.0.3
 * @since 4.0.1
 * @author gizmore
 */
class GDT_Gender extends GDT_Enum
{

// 	const NONE = 'none';
	final public const MALE = 'male';
	final public const FEMALE = 'female';

	protected function __construct()
	{
		parent::__construct();
		$this->icon('gender');
		$this->label('gender');
		$this->enumValues(self::MALE, self::FEMALE);
		$this->emptyLabel('not_specified');
	}

}
