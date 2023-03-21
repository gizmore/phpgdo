<?php
namespace GDO\Crypto;

/**
 * BCrypt crypto utility.
 *
 * @version 7.0.0
 * @since 6.0.0
 * @author gizmore
 */
final class BCrypt
{

	###############
	### Factory ###
	###############
	public $hash;

	public function __construct($hash)
	{
		$this->hash = $hash;
	}

	###############
	### Members ###
	###############

	public static function create($plaintext)
	{
		return new self(password_hash($plaintext, PASSWORD_BCRYPT, self::options()));
	}

	public static function options()
	{
		return [
			'cost' => Module_Crypto::instance()->cfgBCryptCost(),
		];
	}

	public function __toString()
	{
		return $this->hash;
	}

	public function validate($password): bool
	{
		return password_verify($password, $this->hash);
	}

}
