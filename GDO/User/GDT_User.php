<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\DB\Query;
use GDO\Table\GDT_Filter;

/**
 * An autocomplete enabled user field.
 *
 * Settings:
 * - ghost(): fallback to ghost user for null
 * - deleted(): also include deleted users
 * - fallbackCurrentUser(): fallback to current user for null
 * - withPermission(): only allow users with a certain permission
 * - withType(): only allow users of a certain type
 *
 * @TODO: rename fallbackCurrentUser()
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
class GDT_User extends GDT_Object
{

	public bool $deleted = false;
	public bool $fallbackCurrentUser = false;
	public string $withType;

	#############
	### Ghost ###
	#############
	public string $withPermission;
	public bool $noFilter = false;
	public bool $ghost = false;

	protected function __construct()
	{
		parent::__construct();
		$this->table(GDO_User::table());
		$this->icon('face');
	}

	public function ghost(bool $ghost = true): static
	{
		$this->ghost = $ghost;
		return $this;
	}

	###############
	### Deleted ###
	###############

	public function gdtDefaultLabel(): ?string
	{
		return 'user';
	}

	public function withCompletion(): static
	{
		return $this->completionHref(href('User', 'Completion', '&_fmt=json'));
	}

	###############
	### Current ###
	###############

	/**
	 * Allow deleted users to be selected.
	 */
	public function deleted(bool $deleted = true): static
	{
		$this->deleted = $deleted;
		return $this;
	}

	public function fallbackCurrentUser(bool $fallbackCurrentUser = true): static
	{
		$this->fallbackCurrentUser = $fallbackCurrentUser;
		return $this;
	}

	############
	### Type ###
	############

	public function withType(string $withType): static
	{
		$this->withType = $withType;
		return $this;
	}

	public function withPermission(string $withPermission): static
	{
		$this->withPermission = $withPermission;
		return $this;
	}

	############
	### Perm ###
	############

	public function noFilter(bool $noFilter = true): static
	{
		$this->noFilter = $noFilter;
		return $this;
	}


	#############
	### Value ###
	#############


	/**
	 * Get selected user.
	 */
	public function getUser(): ?GDO_User
	{
		$user = $this->getValue();
		return $user ?? null;
	}

	public function getValue(): mixed
	{
		if ($user = parent::getValue())
		{
			return $user;
		}
		elseif ($this->fallbackCurrentUser)
		{
			return GDO_User::current();
		}
		elseif ($this->ghost)
		{
			return GDO_User::ghost();
		}
		return null;
	}

	protected function getGDOsByName(string $var): array
	{
		$field = 'user_name';
		$p = GDO_User::GUEST_NAME_PREFIX;
		if ($var[0] === $p)
		{
			$field = 'user_guest_name';
		}
		$var = GDO::escapeSearchS(trim($var, "$p \t\r\n"));
		$query = GDO_User::table()->select()->
		where("{$field} LIKE '%{$var}%'")->
		limit(GDT_Object::MAX_SUGGESTIONS);
		return $query->exec()->fetchAllObjects();
	}

	################
	### Validate ###
	################
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
        if ($this->hasError())
        {
            return false;
        }

		/** @var GDO_User $user * */
		$user = $value;

		if (!parent::validate($value))
		{
			# Error in parent
			return false;
		}

		if ($value === null)
		{
			# Null check passed already
			return true;
		}

		if (isset($this->withType))
		{
			if ($user->getType() !== $this->withType)
			{
				$typelabel = t('enum_' . $this->withType);
				return $this->error('err_user_type', [$typelabel]);
			}
		}

		if (isset($this->withPermission))
		{
			if (!$user->hasPermission($this->withPermission))
			{
				$permlabel = t('perm_' . $this->withPermission);
				return $this->error('err_user_no_permission', [$permlabel]);
			}
		}

		if (!$this->deleted)
		{
			if ($user->isDeleted())
			{
				return $this->error('err_user_deleted', [$user->gdoDisplay('user_deletor'), $user->gdoDisplay('user_deleted')]);
			}
		}

		return true;
	}

	public function plugVars(): array
	{
		return [
			[$this->getName() => '4'], # monika
		];
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		if ($user = $this->getUser())
		{
			return $user->renderProfileLink();
		}
		return t('unknown');
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
		return $this->renderHTML();
	}

	##############
	### Filter ###
	##############


	public function filterQuery(Query $query, GDT_Filter $f): static
	{
		if (!$this->noFilter)
		{
			if ($filter = $this->filterVar($f))
			{
				$filter = GDO::escapeSearchS($filter);
				$filter = "LIKE '%{$filter}%'";
				$this->filterQueryCondition($query,
					"user_name $filter OR user_guest_name $filter");
			}
		}
		return $this;
	}

	###########
	### CLI ###
	###########
	public function gdoExampleVars(): ?string
	{
		return 'giz|tehr|liv|d';
	}

}
