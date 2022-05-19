<?php
namespace GDO\Crypto;

use GDO\Util\BCrypt;

/**
 * Bcrypt hash form and database value
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_PasswordHash extends GDT_Password
{
    public function isSerializable() : bool { return false; }
    
    public function toValue($var)
	{
		return $var === null ? null : new BCrypt($var);
	}

}
