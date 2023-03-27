<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDT_Name;

/**
 * Username field without completion.
 * Can validate on existing, not-existing and both allowed (null)
 *
 * @version 7.0.3
 * @since 5.0.0
 * @see GDT_User
 * @author gizmore
 */
class GDT_Username extends GDT_Name
{

	final public const LENGTH = 32;

	public ?int $min = 2;

	public ?int $max = self::LENGTH;

	public string $icon = 'face';

	# Allow - _ LETTERS DIGITS
	public string $pattern = "/^[\\p{L}][-_\\p{L}0-9]+$/iuD";

	public ?bool $exists = null;

	public bool $caseSensitive = false;


	##############
	### Exists ###
	##############

	public function defaultLabel(): self
	{
		return $this->label('username');
	}

	public function renderCLI(): string
	{
		return isset($this->gdo) ?
			$this->gdo->renderName() :
			$this->renderHTML();
	}

	##############
	### Render ###
	##############

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (!parent::validate($value))
		{
			return false;
		}

		# Check existance
		if ($this->exists === true)
		{
			if ($user = GDO_User::getByName($value))
			{
				$this->gdo = $user;
			}
			else
			{
				return $this->error('err_user');
			}
		}
		elseif ($this->exists === false)
		{
			if (GDO_User::getByName($value))
			{
				return $this->error('err_username_taken');
			}
		}

		return true;
	}

	################
	### Validate ###
	################

	public function plugVars(): array
	{
		return [
			[$this->getName() => 'Lazer'],
		];
	}

	public function exists(?bool $exists): static
	{
		$this->exists = $exists;
		return $this;
	}

}
