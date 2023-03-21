<?php
namespace GDO\Crypto;

use GDO\Core\GDT;

/**
 * Bcrypt hash form and database value
 *
 * @version 7.0.2
 * @since 5.0.0
 * @author gizmore
 */
class GDT_PasswordHash extends GDT_Password
{

	public function isSerializable(): bool
	{
		return false;
	}

	public function toValue($var = null)
	{
		return $var === null ? null : new BCrypt($var);
	}

	/**
	 * Do not show previous input!
	 */
	public function renderCell(): string
	{
		return GDT::EMPTY_STRING;
	}

//	public function inputToVar($input) : ?string
//	{
//		if ($input === null)
//		{
//			return null;
//		}
//		return (new BCrypt($input))->__toString();
//	}

	public function htmlValue(): string
	{
		return GDT::EMPTY_STRING;
	}

	public function getHash()
	{
		return $this->getValue()->__toString();
	}

}
