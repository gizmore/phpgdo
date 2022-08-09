<?php
namespace GDO\Crypto;

/**
 * Bcrypt hash form and database value
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class GDT_PasswordHash extends GDT_Password
{
    public function isSerializable() : bool { return false; }
    
    public function toValue($var = null)
	{
		return $var === null ? null : new BCrypt($var);
	}
	
	public function getHash()
	{
		return $this->getValue()->__toString();
	}
	
	public function inputToVar(string $input=null) : string
	{
		if ($input === null)
		{
			return null;
		}
		return (new BCrypt($input))->__toString();
	}
	
	/**
	 * Do not show previous input!
	 */
	public function htmlValue() : string
	{
		return '';
	}

}
