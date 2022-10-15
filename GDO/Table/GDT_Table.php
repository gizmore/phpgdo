<?php
namespace GDO\Table;

use GDO\DB\ArrayResult;
use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\UI\WithHREF;
use GDO\UI\WithText;
use GDO\UI\WithTitle;
use GDO\Form\WithActions;
use GDO\Core\GDO_Exception;
use GDO\Form\WithCrud;
use GDO\Core\WithGDO;
use GDO\Form\WithAction;
use GDO\Core\WithName;
use GDO\Core\WithInput;
use GDO\UI\WithPHPJQuery;
use GDO\Core\Application;

/**
 * A filterable, searchable, orderable, paginatable, sortable collection of GDT[] in headers.
 *
 * WithHeaders GDT control provide the filtered, searched, ordered, paginated and sorted.
 * GDT_Pagemenu is used for paginatable.
 *
 * Supports queried Result and ArrayResult.
 *
 * Searched can crawl multiple fields at once via huge query.
 * Filtered can crawl on individual fields.
 * Ordered enables ordering by fields.
 * Paginated enables pagination via GDT_Pagemenu.
 * Sorted enables drag and drop sorting via GDT_Sort and Table::Method::Sorting.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see GDO
 * @see GDT
 * @see GDT_PageMenu
 * @see Result
 * @see ArrayResult
 * @see MethodQueryTable
 */
class GDT_Table extends GDT
{
	use WithGDO;
	use WithCrud;
	use WithName;
	use WithHREF;
	use WithText;
	use WithTitle;
	use WithInput;
	use WithAction;
	use WithActions;
	use WithHeaders;
	use WithPHPJQuery;

	public function getDefaultName() : string
	{
		return 'table';
	}

	# ##########
	# ## GDT ###
	# ##########
	public function isTestable() : bool { return false; }
	
	protected function __construct()
	{
		parent::__construct();
		$this->action = urldecode($_SERVER['REQUEST_URI']);
	}

	# #############
	# ## Footer ###
	# #############
	public GDT $footer;

	public function footer(GDT $footer): self
	{
		$this->footer = $footer;
		return $this;
	}

	# #################
	# ## Hide empty ###
	# #################
	public bool $hideEmpty = false;

	public function hideEmpty(bool $hideEmpty = true): self
	{
		$this->hideEmpty = $hideEmpty;
		return $this;
	}

	# #####################
	# ## Drag&Drop sort ###
	# #####################
	private string $sortableURL;

	public function sorted(string $sortableURL = null)
	{
		if ($sortableURL)
		{
			$this->sortableURL = $sortableURL;
		}
		else
		{
			unset($this->sortableURL);
		}
		return $this;
	}

	# ################
	# ## Searching ###
	# ################
	public bool $searched = false;
	public function searched(bool $searched = true)
	{
		$this->searched = $searched;
		return $this;
	}

	# ################
	# ## Filtering ###
	# ################
	public bool $filtered = false;
	public GDT_Filter $filter;

	public function filtered(bool $filtered = true, GDT_Filter $filter = null)
	{
		$this->filtered = $filtered;
		unset($this->filter);
		if ($filter)
		{
			$this->filter = $filter;
		}
		return $this;
	}

	# ###############
	# ## Ordering ###
	# ###############
	public GDT_Order $order;
	
	public function ordered(GDT_Order $order)
	{
		$this->order = $order;
		$order->orders($this->getOrders($order->getVar()));
		return $this;
	}
	
// 	public function orderFields(array $fields, string $order) : self
// 	{
// 		$this->order->setFields($fields);
// 		$this->order->orders($this->getOrders($order));
// 		return $this;
// 	}
	
	public function isOrdered() : bool { return isset($this->order); }
	public function isOrderable() : bool { return isset($this->order); }
	
// 	protected function getOrderField() : GDT_Order
// 	{
// 		if (!isset($this->order))
// 		{
// 			$this->order = GDT_Order::make("{$this->name}_order");
// 		}
// 		return $this->order;
// 	}
	
	/**
	 * Render an order header for a gdt.
	 * Table only rendering method.
	 */
	public function renderTableOrder(GDT $gdt) : string
	{
		return GDT_Template::php('Table', 'table_order.php', [
			'table' => $this,
			'field' => $gdt,
			'order' => $this->order->href($this->href),
		]);
	}

	# #################
	# ## Pagination ###
	# #################
	public GDT_PageMenu $pagemenu;
	
	public function paginateDefault($href = null)
	{
		return $this->paginated(true, $href,
		Module_Table::instance()->cfgItemsPerPage());
	}

	public function paginated(bool $paginated = true, string $href = null, int $ipp = 0) : self
	{
		unset($this->pagemenu);
		if ($paginated)
		{
			$ipp = $ipp < 1 ? Module_Table::instance()->cfgItemsPerPage() : $ipp;
			$href = $href === null ? $this->action : $href;
			$this->pagemenu = $this->getPageMenu();
			$this->pagemenu->href($href);
			$this->pagemenu->ipp($ipp);
			$this->pagemenu->page(1);
		}
		return $this;
	}
	
	public function isFirstPage(): bool
	{
		return isset($this->pagemenu) OR $this->pagemenu->getPage() === 1;
	}

// 	# ###################
// 	# ## ItemsPerPage ###
// 	# ###################
// // 	public GDT_IPP $ipp;
	
// 	public function ipp(int $ipp) : self
// 	{
// 		$this->getPageMenu()->ipp($ipp);
// 		return $this;
// 	}

	##############
	### Result ###
	##############
	public Result $result;
	
	public function result(Result $result) : self
	{
		$this->result = $result;
		return $this;
	}

	public function getResult() : Result
	{
		if (!isset($this->result))
		{
			$this->result = $this->queryResult();
		}
		return $this->result;
	}

	#############
	### Query ###
	#############
	public Query $query;
	public function query(Query $query)
	{
		$this->query = $this->getFilteredQuery($query);
		return $this;
	}

	public $countQuery;
	public function countQuery(Query $query)
	{
		$this->countQuery = $this->getFilteredQuery($query->copy());
		return $this;
	}

	private bool $filtersApplied = false;

	public function getFilteredQuery(Query $query) : Query
	{
		if ($this->filtered)
		{
			foreach ($this->getHeaderFields() as $gdt)
			{
				if ($gdt->isFilterable())
				{
					$gdt->filterQuery($query, $this->filter);
				}
			}
		}

		if ($this->searched)
		{
// 			$s = $this->headers->name;
// 			if (isset($_REQUEST[$s]['search']))
// 			{
// 				if ($searchTerm = trim($_REQUEST[$s]['search'], "\r\n\t "))
// 				{
// 					$this->bigSearchQuery($query, $searchTerm);
// 				}
// 			}
		}

		return $this->getOrderedQuery($query);
	}

	private function getOrderedQuery(Query $query) : Query
	{
		if (isset($this->order))
		{
			$this->order->orderQuery($query);
		}
		return $query;
	}

	/**
	 * Build a huge where clause for quicksearch.
	 * Supports multiple terms at once, split via whitespaces.
	 * Objects that are searchable JOIN automatically and offer more searchable fields.
	 * In general, GDT_String and GDT_Int is searchable.
	 *
	 * @todo GDT_Enum is not searchable yet.
	 */
	public function bigSearchQuery(Query $query, string $searchTerm) : Query
	{
		$split = preg_split("/\\s+/iD", trim($searchTerm, "\t\r\n "));
		$first = true;
		foreach ($split as $searchTerm)
		{
			$where = [];
			foreach ($this->gdtTable->gdoColumnsCache() as $gdt)
			{
				if ($gdt->searchable)
				{
					if ($condition = $gdt->searchQuery($query, $searchTerm,
					$first))
					{
						$where[] = $condition;
					}
				}
			}
			if ($where)
			{
				$query->where(implode(' OR ', $where));
			}
			$first = false;
		}
	}

	#############
	### Count ###
	#############
	public int $countItems;

	/**
	 * @return int the total number of matching rows.
	 */
	public function countItems() : int
	{
		if (!isset($this->countItems))
		{
			if ($this->countQuery)
			{
				$this->countItems = (int) $this->countQuery->selectOnly('COUNT(*)')
					->noOrder()
					->noLimit()
					->first()
					->exec()
					->fetchValue();
			}
			else
			{
				$this->countItems = $this->getResult()->numRows();
			}
		}
		return $this->countItems;
	}

	public function queryResult() : ?Result
	{
		if (isset($this->query))
		{
			return $this->query->exec();
		}
		return null;
	}

	/**
	 * Create the pagemenu.
	 */
	private function getPageMenu() : GDT_PageMenu
	{
		if (!isset($this->pagemenu))
		{
			$this->pagemenu = GDT_PageMenu::make();
		}
		return $this->pagemenu;
	}

	################
	### Fetch As ###
	################
	public GDO $fetchAs;

	public function gdo(GDO $gdo=null) : self
	{
		if ($gdo === null)
		{
			unset($this->fetchAs);
		}
		else
		{
			$this->fetchAs = $gdo;
		}
		return parent::gdo($gdo);
	}
	
	public function fetchAs(GDO $fetchAs = null) : self
	{
		if ($fetchAs)
		{
			$this->fetchAs = $fetchAs;
		}
		else
		{
			unset($this->fetchAs);
		}
		return $this;
	}

	# #############
	# ## Render ###
	# #############
	public function renderHTML() : string
	{
		if (($this->hideEmpty) && ($this->getResult()->numRows() === 0))
		{
			return GDT::EMPTY_STRING;
		}
		try
		{
			$mode = Application::$MODE;
			Application::$MODE = GDT::RENDER_CELL;
			return GDT_Template::php('Table', 'table_html.php',
			[
				'field' => $this,
				'form' => false,
			]);
		}
		finally
		{
			Application::$MODE = $mode;
		}
	}

	public function renderForm() : string
	{
		return GDT_Template::php('Table', 'cell/table.php',
		[
			'field' => $this,
			'form' => true,
		]);
	}

	public function renderCard() : string
	{
		return $this->renderHTML();
	}

	public function renderJSON()
	{
		$json = array_merge($this->configJSON(),
		[
			'data' => $this->renderJSONData(),
		]);
		return $json;
	}

	public function configJSON() : array
	{
		return array_merge(parent::configJSON(),
		[
			'tableName' => isset($this->gdo) ? $this->gdo->gdoClassName() : null,
			'pagemenu' => isset($this->pagemenu) ? $this->getPageMenu()->configJSON() : null,
			'total' => (int) (isset($this->pagemenu) ? $this->pagemenu->numItems : $this->getResult()->numRows()),
			'searched' => $this->searched,
			'searchable' => $this->isSearchable(),
			'sorted' => $this->sorted,
			'sortableURL' => $this->sortableURL,
			'filtered' => $this->filtered,
			'filterable' => $this->isFilterable(),
			'ordered' => isset($this->order),
			'orderable' => $this->isOrderable(),
			'orderDefault' => $this->isOrderable() ? $this->order->getVar() : null,
		]);
	}

	protected function renderJSONData()
	{
		$data = [];
		$result = $this->getResult();
		$table = $result->table;
		while ($gdo = $table->fetch($result))
		{
			$dat = [];
			foreach ($this->getHeaderFields() as $gdt)
			{
				if ($gdt->name && $gdt->isSerializable())
				{
					$json = $gdt->gdo($gdo)->renderJSON();
					if (is_array($json))
					{
						foreach ($json as $k => $v)
						{
							$dat[$k] = $v;
						}
					}
					else
					{
						$dat[$gdt->name] = $json;
					}
				}
			}
			$data[] = $dat;
		}
		return $data;
	}

	public function renderXML() : string
	{
		$xml = "<data>\n";
		$result = $this->getResult();
		while ($gdo = $result->fetchObject())
		{
			$xml .= "<row>\n";
			foreach ($this->getHeaderFields() as $gdt)
			{
				if ($gdt->hasName() && $gdt->isSerializable())
				{
					$xml .= $gdt->gdo($gdo)->renderXML();
				}
			}
			$xml .= "</row>\n";
		}
		$xml .= "</data>\n";
		return $xml;
	}

	public function renderCLI() : string
	{
		# Collect
		$items = [];
		$result = $this->getResult();
		while ($gdo = $result->fetchObject())
		{
			$items[] = $gdo->renderCLI();
		}

		# Print either single page or pages.
		$p = isset($this->pagemenu) ? $this->pagemenu : false;
		if ($p && $p->getPageCount() > 1)
		{
			return t('cli_pages',
			[
				$this->renderTitle(),
				$p->getPage(),
				$p->getPageCount(),
				implode(', ', $items)
			]);
		}
		else
		{
			return t('cli_page', [
				$this->renderTitle(),
				implode(', ', $items)
			]);
		}
	}

	# ###############
	# ## Page for ###
	# ###############
	/**
	 * Calculate the page for a gdo.
	 * We do this by examin the order from our filtered query.
	 * We count(*) the elements that are before or after orderby.
	 */
	public function getPageFor(GDO $gdo)
	{
		$result = $this->getResult();
		
		if ($result instanceof ArrayResult)
		{
			throw new GDO_Exception("@TODO implement getPageFor() ArrayResult");
		}
		else
		{
			$q = $this->query->copy(); # ->noJoins();
			if (isset($this->order))
			{
				$i = 0;
				foreach ($this->order->getAllFields() as $field)
				{
					$i++;
					$column = $field->getName();
					$subq = $gdo->entityQuery()
						->from($gdo->gdoTableName() . " AS sq{$i}")
						->selectOnly($column)
						->buildQuery();
					$order = stripos($column, 'DESC') ? '0' : '1';
					$cmpop = $order ? '<' : '>';
					$q->where("{$column} {$cmpop} ( {$subq} )");
				}
			}
			$q->selectOnly('COUNT(*)')->noOrder();
			$itemsBefore = $q->exec()->fetchValue();
			return $this->getPageForB($itemsBefore);
		}
	}

	private function getPageForB($itemsBefore)
	{
		$ipp = $this->getPageMenu()->ipp;
		return intval(($itemsBefore + 1) / $ipp) + 1;
	}

}
