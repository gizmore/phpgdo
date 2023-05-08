<?php
declare(strict_types=1);
namespace GDO\Crypto;

use GDO\Core\GDT_String;

/**
 * Bcrypt hash form and database value.
 *
 * @version 7.0.0
 * @since 5.0
 * @author gizmore
 */
class GDT_Password extends GDT_String
{

	protected function __construct()
	{
		parent::__construct();
		$this->min = 59;
		$this->max = 60;
		$this->encoding = self::ASCII;
		$this->caseSensitive = true;
		$this->icon('lock');
		$this->tooltip('tt_password');
	}

	public function isSerializable(): bool { return true; }

	public function isSearchable(): bool { return false; }

	public function isOrderable(): bool { return false; }

	public function getInputType(): string
	{
		return 'password';
	}

	public function defaultLabel(): self { return $this->label('password'); }

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var === null ? null : new BCrypt($var);
	}

	public function getGDOData(): array
	{
		$pass = $this->getValue();
		return [$this->name => $pass ? $pass->__toString() : null];
	}

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if ($value === null || (!$value->hash))
		{
			return !$this->notNull || $this->errorNull();
		}
		elseif (mb_strlen($value->__toString()) < 4)
		{
			return $this->error('err_pass_too_short', [4]);
		}
		return true;
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
		return null;
	}

}
