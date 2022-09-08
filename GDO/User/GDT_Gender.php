<?php
namespace GDO\User;

use GDO\Core\GDT_Enum;

/**
 * Gender enum.
 * 
 * @author gizmore
 * @version 6.0.7
 * @since 4.0.1
 */
class GDT_Gender extends GDT_Enum
{
// 	const NONE = 'none';
	const MALE = 'male';
	const FEMALE = 'female';
	
// 	public function defaultLabel() : self { return ; }
	
	protected function __construct()
	{
		parent::__construct();
		$this->icon('gender');
		$this->label('gender');
// 		$this->enumValues(self::NONE, self::MALE, self::FEMALE);
		$this->enumValues(self::MALE, self::FEMALE);
// 		$this->initial(self::NONE);
// 		$this->notNull();
	}
	
	public function enumLabel($enumValue=null)
	{
		return t("gender_$enumValue");
	}

}
