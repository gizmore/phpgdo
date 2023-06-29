<?php
declare(strict_types=1);
namespace GDO\Table;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Tuple;
use GDO\DB\ArrayResult;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\UI\GDT_SearchField;
use GDO\User\GDO_User;

/**
 * A method that displays a table from memory via ArrayResult.
 * It's the base class for MethodQueryTable or MethodQueryCards.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 * @see ArrayResult
 * @see GDO
 * @see GDT
 * @see GDT_Table
 * @see GDT_Order
 * @see GDT_Filter
 * @see GDT_SearchField
 */
abstract class MethodTable extends MethodForm
{

	public GDT_Table $table;

	# Override Feature Parameter Names.
	/**
	 * @var GDT[]
	 */
	private array $headerCache;

	/**
	 * Override this.
	 * Called upon creation of the GDT_Table.
	 */
	public function onCreateTable(GDT_Table $table): void {}

	public function getFilterVars(): array
	{
		return $this->getFilterField()->getFilterVars();
	}

	public function getFilterField(): ?GDT_Filter
	{
		return $this->gdoParameter($this->getFilterName(), false, false);
	}

	protected function getFilterName(): string { return 'f'; }

	public function validate(): bool
	{
		$valid = true;
		foreach ($this->gdoParameterCache() as $gdt)
		{
			if (!$gdt->validated())
			{
				$valid = false;
			}
		}
		return $valid;
	}

	/**
	 * @return GDT[]
	 */
	public function &gdoParameterCache(): array
	{
		if (!isset($this->parameterCache))
		{
			parent::gdoParameterCache();
			$this->addComposeParameters($this->gdoTableFeatures());
		}
		return $this->parameterCache;
	}

	/**
	 * Table features paramter array.
	 * @return GDT[]
	 */
	private function gdoTableFeatures(): array
	{
		$features = [];
		if ($this->isPaginated())
		{
			$features[] = GDT_IPP::make($this->getIPPName())->initialValue($this->getDefaultIPP());
			$features[] = GDT_PageNum::make($this->getPageName())->initial('1');
		}
		if ($this->isSearched())
		{
			$features[] = GDT_SearchField::make($this->getSearchName());
		}
		if ($this->isOrdered())
		{
			$features[] = GDT_Order::make($this->getOrderName())->
			initial($this->getDefaultOrder())->
			extraFields($this->getExtraFieldNames())->
			setFields($this->gdoHeaderCache());
		}
		if ($this->isFiltered())
		{
			$features[] = GDT_Filter::make($this->getFilterName());
		}
		return $features;
	}

	public function isPaginated(): bool { return true; }

	protected function getIPPName(): string { return 'ipp'; }

	/**
	 * Default IPP defaults to config in Module_Table.
	 *
	 * @see Module_Table::getConfig()
	 */
	public function getDefaultIPP(): int { return Module_Table::instance()->cfgItemsPerPage(); }

	protected function getPageName(): string { return 'page'; }

	/**
	 * Override this.
	 * Return true if this table shall be searchable over all columns with one input field.
	 * This is called "HugeQuery" in the GDT_Table implementation.
	 *
	 * @return bool
	 */
	public function isSearched(): bool { return true; }

	protected function getSearchName(): string { return 'search'; }

	/**
	 * Override this.
	 * Return true if this table shall be able to be ordered by headers.
	 */
	public function isOrdered(): bool { return true; }

	protected function getOrderName(): string { return 'o'; }

	public function getDefaultOrder(): ?string
	{
		foreach ($this->gdoHeaderCache() as $gdt)
		{
			if ($gdt->isOrderable())
			{
				return $gdt->name . ($gdt->isDefaultAsc() ? ' ASC' : ' DESC');
			}
		}
		return null;
	}

	public function gdoHeaderCache(): array
	{
		if (!isset($this->headerCache))
		{
			$this->headerCache = [];
			foreach ($this->gdoHeaders() as $gdt)
			{
				if ($name = $gdt->getName())
				{
					$this->headerCache[$name] = $gdt;
				}
				else
				{
					$this->headerCache[] = $gdt;
				}
			}
		}
		return $this->headerCache;
	}

	/**
	 * Override this.
	 * Return an array of GDT[] for the table headers.
	 * Defaults to all fields from your gdoTable().
	 *
	 * @return GDT[]
	 */
	public function gdoHeaders(): array
	{
		return $this->gdoTable()->gdoColumnsCache();
	}

	/**
	 * Override this with returning your GDO->table()
	 */
	abstract public function gdoTable(): GDO;

	################
	### Abstract ###
	################

	public function getExtraFieldNames(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	/**
	 * Override this.
	 * Return true if you want to be able to filter your data by your header columns.
	 *
	 * @return bool
	 */
	public function isFiltered(): bool { return true; }

	public function execute(): GDT
	{
		$form = $this->getForm();
		if ($form->isEmpty())
		{
			return GDT_Tuple::makeWith(
				$this->renderTable());
		}
		return GDT_Tuple::makeWith(
			$form,
			$this->renderTable());
	}

	public function renderTable(): GDT_Table
	{
		return $this->getTable();
	}

	public function getTable(): GDT_Table
	{
		if (!isset($this->table))
		{
			$this->table = $this->createCollection();
			$this->initTable();
		}
		return $this->table;
	}

	##################
	### 5 features ###
	##################

	/**
	 * Creates the collection GDT.
	 */
	protected function createCollection(): GDT_Table
	{
		$this->table = GDT_Table::make($this->getTableName());
		$this->table->href($this->gdoTableHREF());
		$this->table->gdo($this->gdoTable());
		$this->table->fetchAs($this->gdoFetchAs());
		$this->gdoParameterCache();
		return $this->table;
	}

	protected function getTableName(): string { return 'table'; } # GDT$searchable

	protected function gdoTableHREF(): string { return $this->href(); } # GDT#filterable

	public function gdoFetchAs(): GDO { return $this->gdoTable(); } # creates a GDT_Pagemenu

	private function initTable(): void
	{
		$table = $this->table;
		$this->setupCollection($table);
		$this->onInitTable();
		$this->beforeCalculateTable($table);
		$this->calculateTable($table);
		$result = $table->getResult();
		$result->table = $this->gdoFetchAs();
		$this->setupTitle($table);
	} # Uses js/ajax and GDO needs to have GDT_Sort column.

	############
	### CRUD ###
	############

	protected function setupCollection(GDT_Table $table): void
	{
		$headers = $this->gdoHeaderCache();
		$this->table->addHeaderFields(...array_values($headers));

		# 5 features
		if ($this->isOrdered())
		{
			$table->ordered($this->gdoParameter($this->getOrderName()));
		}
		$table->filtered($this->isFiltered(), $this->getFilterField());
		$table->searched($this->isSearched());
//		$table->sorted($this->isSorted());
		if ($this->isPaginated())
		{
			$table->paginated(true, $this->gdoTableHREF(), $this->getIPP());
			$this->gdoParameter($this->getPageName())->table($this->table);
		}

		# 4 editor permissions
		$user = GDO_User::current();
		$table->creatable($this->isCreateable($user));
		$table->readable($this->isReadable($user));
		$table->updatable($this->isUpdateable($user));
		$table->deletable($this->isDeleteable($user));

		# 1 speedup
		$table->fetchInto($this->useFetchInto());
		$table->fetchAs($this->gdoFetchAs());
	}

	/**
	 * Override this.
	 * Return true if you want to be able to sort this table data manually.
	 * This requires a GDT_Sort field in your GDO columns / headers as well as MethodSort endpoint.
	 * @see GDT_Sort
	 * @see MethodSort
	 */
	public function isSorted(): bool { return false; }

	public function getIPP(): int
	{
		return $this->gdoParameterValue($this->getIPPName());
	}

	public function getPage(): int
	{
		return $this->gdoParameterValue($this->getPageName());
	}

	public function isCreateable(GDO_User $user): bool { return false; }

	public function isReadable(GDO_User $user): bool { return false; }

	public function isUpdateable(GDO_User $user): bool { return false; }

	public function isDeleteable(GDO_User $user): bool { return false; }

	public function useFetchInto(): bool { return true; }

	protected function onInitTable(): void {}

	###############
	### Execute ###
	###############

	protected function beforeCalculateTable(GDT_Table $table): void
	{
		if ($this->isPaginated())
		{
			$result = $this->getResult();
			$table->pagemenu->pageName = $this->getPageName();
			$table->pagemenu->numItems(count($result->getData()));
			$table->pagemenu->page($this->getPage());
		}
	}

	/**
	 * Override this with returning an ArrayResult with data.
	 */
	public function getResult(): ArrayResult { return new ArrayResult(GDT::EMPTY_ARRAY, $this->gdoTable()); }

	protected function calculateTable(GDT_Table $table): void
	{
		# Exec
		$result = $this->getResult();

		# Exec features
		if ($this->isFiltered())
		{
			$result = $result->filterResult($result->getFullData(), $table->getHeaderFields(), $this->getFilterField());
		}
		if ($this->isSearched())
		{
			$result = $result->searchResult($result->getData(), $this->gdoTable(), $table->getHeaderFields(), $this->getSearchTerm());
		}
		if ($this->isOrdered())
		{
			$table->result($result);
			$result = $table->multisort($this->getOrderTerm());
		}
		if ($this->isPaginated())
		{
			$result = $table->pagemenu->paginateResult($result, $this->getPage(), $this->getIPP());
		}
		$table->result($result);
	}

	public function getSearchTerm(): string
	{
		return (string) $this->gdoParameterVar($this->getSearchName());
	}

	public function getOrderTerm(): string
	{
		return $this->gdoParameterValue($this->getOrderName());
	}

	protected function setupTitle(GDT_Table $table): void
	{
		$table->titleRaw($this->getTableTitle());
	}

	public function getTableTitle(): string
	{
		if (isset($this->table))
		{
			$key = $this->getTableTitleLangKey();
			return t($key, [$this->table->countItems()]);
		}
		else
		{
			$key = "mt_{$this->getModuleName()}_{$this->getMethodName()}";
			return t(strtolower($key));
		}
	}

	public function getTableTitleLangKey(): string
	{
		return strtolower('list_' . $this->getModuleName() . '_' . $this->getMethodName());
	}

	public function getMethodTitle(): string
	{
		return $this->getTableTitle();
	}

	public function onMethodInit(): ?GDT
	{
//		$this->getTable();
		return null;
	}

	public function isUserRequired(): bool
	{
		return false;
	}

	protected function createForm(GDT_Form $form): void {}

	### Form

	public function getNumItems(): int
	{
		return $this->table->countItems();
	}

	protected function getCurrentHREF(): string
	{
		return $_SERVER['REQUEST_URI'];
	}

}
