<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\Query;
use GDO\Table\GDT_Filter;
use GDO\UI\TextStyle;

/**
 * You would expect this to be in GDT_Object,
 * but this is also mixed into GDT_ObjectSelect, hence it is a trait.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 * @see GDT_Object
 * @see GDT_ObjectSelect
 */
trait WithObject
{

	use WithGDO;

	# ##################
	# ## With Object ###
	# ##################
	public const CASCADE_NULL = 'SET NULL';

	###############
	### Cascade ###
	###############
	public const CASCADE_NO = 'RESTRICT';
	public const CASCADE = 'CASCADE';
	public GDO $table;
	public string $filterField;

	# ##################
	# ## Var / Value ###
	# ##################
	/**
	 * Cascade mode for foreign keys.
	 * Default is SET NULL, so nothing gets lost easily.
	 *
	 * Fun bug was:
	 * delete a language => delete all users that use this language
	 * triggered by a replace on install module_language.
	 */
	public string $cascade = self::CASCADE_NULL;

	/**
	 * The GDO table to operate on.
	 */
	public function table(GDO $table): self
	{
		$this->table = $table;
		return $this;
	}

	public function toVar($value): ?string
	{
		return $value ? $value->getID() : null;
	}

	public function displayVar(string $var = null): string
	{
		if (isset($this->multiple) && $this->multiple)
		{
			if ($gdos = $this->toValue($var))
			{
				return implode(', ', array_map(function (GDO $gdo)
				{
					return $gdo->renderName();
				}, $gdos));
			}
			return GDT::EMPTY_STRING;
		}
		/** @var GDO $gdo * */
		if ($gdo = $this->toValue($var))
		{
			if ($gdt = $gdo->gdoNameColumn())
			{
				return html($gdt->getVar());
			}
			else
			{
				return $gdo->renderName();
			}
		}
		else
		{
			return TextStyle::italic(t('none'));
		}
	}

	public function toValue($var = null)
	{
		if ($var !== null)
		{
			if (
				($gdo = $this->table->getById($var)) ||
				($gdo = $this->getByName($var))
			)
			{
				// $this->addInput($this->getName(), $gdo->getID());
				return $gdo;
			}
		}
		return null;
	}

	# #############
	# ## Render ###
	# #############

	/**
	 * Analyze seearch hits for getGDOsByName.
	 * Maybe only one user starts with the input.
	 */
	private function getByName(string $var): ?GDO
	{
		$gdos = $this->getGDOsByName($var);
		if (count($gdos) === 0)
		{
			return null;
		}
		if (count($gdos) === 1)
		{
			return $gdos[0];
		}
		$firsts = [];
		$middles = [];
		foreach ($gdos as $gdo)
		{
			$name = $gdo->getName();
			if (strcasecmp($name, $var) === 0)
			{
				return $gdo;
			}
			if (stripos($name, $var) === 0)
			{
				$firsts[] = $gdo;
			}
			$middles[$name] = $gdo;
		}
		if (count($firsts) === 1)
		{
			return $firsts[0];
		}
		if (count($middles) === 1)
		{
			return $middles[0];
		}
		$this->error('err_select_candidates', [
			implode('|', array_keys($middles)),
		]);
		return null;
	}

	/**
	 * Get possible matching inputs via GDT_Name fields.
	 *
	 * @return GDO[]
	 */
	protected function getGDOsByName(string $var): array
	{
		$query = $this->table->select();
		$gdt = $this->table->gdoColumnOf(GDT_Name::class);
		if ($gdt !== null)
		{
			$var = GDO::escapeSearchS($var);
			$query->where("{$gdt->name} LIKE '%{$var}%'");
			$query->limit(GDT_Object::MAX_SUGGESTIONS);
			return $query->exec()->fetchAllObjects();
		}
		return [];
	}

	public function getVar()
	{
		if (!($var = $this->getInput($this->getName())))
		{
			$var = $this->var;
		}
		return empty($var) ? null : $var;
	}

	# ###############
	# ## Validate ###
	# ###############

	public function getGDOData(): array
	{
		if ($gdo = $this->getValue())
		{
			if (is_object($gdo))
			{
				return [
					$this->name => $gdo->getID(),
				];
			}
			elseif (is_array($gdo))
			{
				return [
					$this->name => json_encode(array_keys($gdo)),
				];
			}
		}
		return [
			$this->name => null,
		];
	}

	public function validate($value): bool
	{
		if ($value) # we successfully converted the var to value.
		{
			return true;
		}
		elseif ($var = $this->getVar()) # 404, as we have a search term.
		{
			if ($this->hasError())
			{
				return false;
			}
			return $this->error('err_gdo_not_found', [
				$this->table->gdoHumanName(),
				html($var),
			]);
		}
		elseif ($this->notNull) # empty input and not null
		{
			return $this->errorNull();
		}
		else # null
		{
			return true;
		}
	}

	public function plugVars(): array
	{
		if (isset($this->table))
		{
			if (@$this->multiple)
			{
				return $this->plugVarsMultiple();
			}
			else
			{
				return $this->plugVarsSingle();
			}
		}
		return GDT::EMPTY_ARRAY;
	}

	private function plugVarsMultiple(): array
	{
		$two = $this->plugVarsSingle();
		$first = isset($two[0]) ? $two[0] : null;
		$second = isset($two[1]) ? $two[1] : null;
		$plugs = [];
		if ($first)
		{
			$plugs[] = [$this->name => json_encode($first)];
		}
		if ($second)
		{
			$plugs[] = [$this->name => json_encode($second)];
		}
		if ($first && $second)
		{
			$json = json_encode([$first, $second]);
			$plugs[] = [$this->name => $json];
		}
		return $plugs;
	}

	# ##############
	# ## Cascade ###
	# ##############

	private function plugVarsSingle(): array
	{
		$back = [];
		if (
			$first = $this->table->select()
				->first()
				->exec()
				->fetchObject()
		)
		{
			$back[] = [$this->name => $first->getID()];
		}
		if (
			$second = $this->table->select()
				->limit(1, 1)
				->exec()
				->fetchObject()
		)
		{
			$back[] = [$this->name => $second->getID()];
		}
		return $back;
	}

	public function renderFilter(GDT_Filter $f): string
	{
		return GDT_Template::php('Core', 'object_filter.php', [
			'field' => $this,
			'f' => $f,
		]);
	}

	/**
	 * Proxy filter to the pk filterColumn if specified.
	 * else filter like parent.??
	 *
	 * @todo check
	 *
	 * @see GDT_Int::filterQuery()
	 * @see GDT_String::filterQuery()
	 */
	public function filterQuery(Query $query, GDT_Filter $f): self
	{
		if (isset($this->filterField))
		{
			$this->table->gdoColumn($this->filterField)->filterQuery($query, $f);
			return $this;
		}
		else
		{
			return parent::filterQuery($query, $f);
		}
	}

	/**
	 * Build a huge quicksearch query.
	 */
	public function searchQuery(Query $query, string $term): self
	{
		return $this;
//		$table = $this->table;
//		$nameT = GDO::escapeIdentifierS('t_' . $this->name);

//		if ($first) // first time joined this table?
//		{
//			$name = GDO::escapeIdentifierS($this->name);
//			$fk = $table->gdoPrimaryKeyColumn()->name;
//			$fkI = GDO::escapeIdentifierS($fk);
//			$myT = $this->gdtTable->gdoTableName();
//			$query->join("LEFT JOIN {$table->gdoTableName()} {$nameT} ON {$myT}.{$name} = {$nameT}.{$fkI}");
//		}

//		$where = [];
//		foreach ($table->gdoColumnsCache() as $gdt)
//		{
//			$gdt->searchQuery($query, $term);
//		}
//		return $this;
	}	public function cascade(): self
	{
		$this->cascade = self::CASCADE;
		return $this;
	}

	public function htmlValue(): string
	{
		if ($var = $this->getVar())
		{
			return sprintf(' value="%s"', html($var));
		}
		return GDT::EMPTY_STRING;
	}

	public function cascadeNull(): self
	{
		$this->cascade = self::CASCADE_NULL;
		return $this;
	}

	#####

	public function cascadeRestrict(): self
	{
		$this->cascade = self::CASCADE_NO;
		return $this;
	}



	/**
	 * If object columns are not null, they cascade upon deletion.
	 */
	public function notNull(bool $notNull = true): self
	{
		$this->notNull = $notNull;
		return $this->cascade();
	}


	/**
	 * If object columns are primary, they cascade upon deletion.
	 */
	public function primary(bool $primary = true): self
	{
		$this->primary = $primary;
		return $this->notNull();
	}

	# #############
	# ## Filter ###
	# #############


	# #############
	# ## Search ###
	# #############


}
