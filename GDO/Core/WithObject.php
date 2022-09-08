<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\UI\TextStyle;
use GDO\Table\GDT_Filter;

/**
 * You would expect this to be in GDT_Object,
 * but this is also mixed into GDT_ObjectSelect, hence it is a trait.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see GDT_Object
 * @see GDT_ObjectSelect
 */
trait WithObject
{
	use WithGDO;

	# ##################
	# ## With Object ###
	# ##################
	public GDO $table;
	/**
	 * The GDO table to operate on.
	 */
	public function table(GDO $table): self
	{
		$this->table = $table;
		return $this;
	}

	# ##################
	# ## Var / Value ###
	# ##################
	public function getVar()
	{
		if ( !($var = $this->getInput($this->name)))
		{
			$var = $this->var;
		}
		return empty($var) ? null : $var;
	}

	public function toVar($value): ?string
	{
		return $value !== null ? $value->getID() : null;
	}

	public function toValue($var = null)
	{
		if ($var !== null)
		{
			if (($gdo = $this->table->getById($var)) ||
				($gdo = $this->getByName($var)))
			{
				// $this->addInput($this->getName(), $gdo->getID());
				return $gdo;
			}
		}
	}

	/**
	 * Get possible matching inputs via GDT_Name fields.
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
			implode('|', array_keys($middles))
		]);
		return null;
	}

	# #############
	# ## Render ###
	# #############
	public function htmlValue(): string
	{
		if ($value = $this->getValue())
		{
			return $value ? sprintf(' value="%s"', $value->getID()) : '';
		}
		return GDT::EMPTY_STRING;
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
		/** @var $gdo GDO **/
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

	public function getGDOData() : array
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
		else
		{
			return [
				$this->name => null,
			];
		}
	}

	# ###############
	# ## Validate ###
	# ###############
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
				html($var)
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
		return [];
	}

	private function plugVarsSingle(): array
	{
		$back = [];
		if ($first = $this->table->select()
			->first()
			->exec()
			->fetchObject())
		{
			$back[] = [$this->name => $first->getID()];
		}
		if ($second = $this->table->select()
			->limit(1, 1)
			->exec()
			->fetchObject())
		{
			$back[] = [$this->name => $second->getID()];
		}
		return $back;
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
	/**
	 * Cascade mode for foreign keys.
	 * Default is SET NULL, so nothing gets lost easily.
	 *
	 * Fun bug was:
	 * delete a language => delete all users that use this language
	 * triggered by a replace on install module_language.
	 *
	 * @var string
	 */
	public $cascade = 'SET NULL';

	public function cascade(): self
	{
		$this->cascade = 'CASCADE';
		return $this;
	}

	public function cascadeNull(): self
	{
		$this->cascade = 'SET NULL';
		return $this;
	}

	public function cascadeRestrict(): self
	{
		$this->cascade = 'RESTRICT';
		return $this;
	}

	#####
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

	# ###############
	# ## Database ###
	# ###############
	/**
	 * Take the foreign key primary key definition and use str_replace to convert to foreign key definition.
	 */
	public function gdoColumnDefine(): string
	{
		if ( !($table = $this->table))
		{
			throw new GDO_Error('err_gdo_object_no_table', [
				$this->identifier()
			]);
		}
		$tableName = $table->gdoTableIdentifier();
		if ( !($primaryKey = $table->gdoPrimaryKeyColumn()))
		{
			throw new GDO_Error('err_gdo_no_primary_key', [
				$tableName,
				$this->identifier()
			]);
		}
		$define = $primaryKey->gdoColumnDefine();
		$define = str_replace($primaryKey->identifier(), $this->identifier(), $define);
		$define = str_replace(' NOT NULL', '', $define);
		$define = str_replace(' PRIMARY KEY', '', $define);
		$define = str_replace(' AUTO_INCREMENT', '', $define);
		$define = preg_replace('#,FOREIGN KEY .* ON UPDATE (?:CASCADE|RESTRICT|SET NULL)#', '', $define);
		// $on = $this->fkOn ? $this->fkOn : $primaryKey->identifier();
		$on = $primaryKey->identifier();
		return "$define{$this->gdoNullDefine()}" .
			",FOREIGN KEY ({$this->identifier()}) REFERENCES $tableName($on) ON DELETE {$this->cascade} ON UPDATE CASCADE";
	}

	# #############
	# ## Filter ###
	# #############
	public string $filterField;
	
	public function renderFilter(GDT_Filter $f): string
	{
		return GDT_Template::php('Core', 'object_filter.php', [
			'field' => $this,
			'f' => $f
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

	# #############
	# ## Search ###
	# #############
	/**
	 * Build a huge quicksearch query.
	 *
	 * @param Query $query
	 * @param string $searchTerm
	 * @param boolean $first
	 * @return string
	 */
	public function searchQuery(Query $query, $searchTerm, $first)
	{
		$table = $this->table;
		$nameT = GDO::escapeIdentifierS('t_' . $this->name);

		if ($first) // first time joined this table?
		{
			$name = GDO::escapeIdentifierS($this->name);
			$fk = $table->gdoPrimaryKeyColumn()->name;
			$fkI = GDO::escapeIdentifierS($fk);
			$myT = $this->gdtTable->gdoTableName();
			$query->join("LEFT JOIN {$table->gdoTableName()} {$nameT} ON {$myT}.{$name} = {$nameT}.{$fkI}");
		}

		$where = [];
		foreach ($table->gdoColumnsCache() as $gdt)
		{
			if ($gdt->searchable)
			{
				if ($condition = $gdt->searchCondition($searchTerm, $nameT))
				{
					$where[] = $condition;
				}
			}
		}
		return implode(' OR ', $where);
	}

}
