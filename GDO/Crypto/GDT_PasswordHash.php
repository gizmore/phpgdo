<?php
namespace GDO\Crypto;

/**
 * Bcrypt hash form and database value
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_PasswordHash extends GDT_Password
{
    public function isSerializable() : bool { return false; }
    
    /**
     * @return BCrypt
     */
    public function toValue(string $var = null)
	{
		return $var === null ? null : new BCrypt($var);
	}
	
	public function getHash()
	{
		return $this->getValue()->__toString();
	}
	
// 	public function getVar() : string
// 	{
// 		return $this->getHash();
// 	}
	
	/**
	 * Do not show previous input!
	 * @return string
	 */
	public function htmlValue() : string
	{
		return '';
	}

}
