<?php
namespace GDO\Crypto;

/**
 * Bcrypt hash form and database value
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_PasswordHash extends GDT_Password
{
    public function isSerializable() : bool { return false; }
    
    public function toValue(string $var = null)
	{
		return $var === null ? null : new BCrypt($var);
	}

}
