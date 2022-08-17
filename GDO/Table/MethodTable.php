<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\ArrayResult;
use GDO\Core\GDT;
use GDO\Core\GDO;
use GDO\User\GDO_User;
use GDO\UI\GDT_SearchField;
use GDO\Core\GDT_Tuple;
use GDO\Admin\Method\Users;
use GDO\Admin\Method\Permissions;
use GDO\Admin\Method\ViewPermission;

/**
 * A method that displays a table from memory via ArrayResult.
 * It's the base class for MethodQueryTable or MethodQueryCards.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 * @see ArrayResult
 * @see GDT_Table
 * @see GDT
 * @see GDO
 */
abstract class MethodTable extends Method
{
	public GDT_Table $table;
	
	# Override Feature Parameter Names.
	protected function getIPPName() : string { return 'ipp'; }
	protected function getPageName() : string { return 'page'; }
	protected function getOrderName() : string { return 'by'; }
	protected function getTableName() : string { return 'table'; }
	protected function getSearchName() : string { return 'search'; }
	
	/**
	 * Override this.
	 * Return an array of GDT[] for the table headers.
	 * Defaults to all fields from your gdoTable().
	 * @return GDT[]
	 */
	public function gdoHeaders() : array { return $this->gdoTable()->gdoColumnsCache(); }
	protected function gdoTableHREF() : string { return $this->href(); }
	
	# event
	protected function onInitTable() : void {}
	
	/**
	 * @return GDT[string]
	 */
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			$this->addComposeParameters($this->gdoParameters());
			$this->addComposeParameters($this->gdoTableFeatures());
		}
		return $this->parameterCache;
	}
	
	private array $headerCache;
	public function gdoHeaderCache() : array
	{
		if (!isset($this->headerCache))
		{
			$this->headerCache = [];
			foreach ($this->gdoHeaders() as $gdt)
			{
				$this->headerCache[$gdt->getName()] = $gdt;
			}
		}
		return $this->headerCache;
	}
	
	/**
	 * Table features paramter array.
	 * @return array
	 */
	private function gdoTableFeatures() : array
	{
		$features = [];
		if ($this->isPaginated())
		{
			$features[] = GDT_IPP::make($this->getIPPName());
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
				setFields($this->gdoHeaderCache());
		}
		if ($this->isFiltered())
		{
		}
		return $features;
	}
	
    ################
    ### Abstract ###
    ################
    /**
     * Override this with returning your GDO->table()
     * @return GDO
     */
    public abstract function gdoTable();
   
    public function gdoFetchAs() { return $this->gdoTable(); }
    
    /**
     * Override this with returning an ArrayResult with data.
     * @return ArrayResult
     */
    public function getResult() : ArrayResult { return new ArrayResult([], $this->gdoTable()); }

    /**
     * Override this to toggle fetchInto speedup in table rendering to reduce GDO allocations.
     * @return boolean
     */
    public function useFetchInto() : bool { return true; }
    
    /**
     * Default IPP defaults to config in Module_Table.
     * @see Module_Table::getConfig()
     * @return string
     */
    public function getDefaultIPP() : int { return Module_Table::instance()->cfgItemsPerPage(); }
    
    /**
     * Override this.
     * Called upon creation of the GDT_Table.
     * @param GDT_Table $table
     */
    public function onCreateTable(GDT_Table $table) : void {}
    
    protected function getCurrentHREF()
    {
    	return $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Creates the collection GDT.
     */
    public function createCollection() : GDT_Table
    {
        $this->table = GDT_Table::make($this->getTableName());
        $this->table->href($this->gdoTableHREF());
        $this->table->gdo($this->gdoTable());
        $this->table->fetchAs($this->gdoFetchAs());
        return $this->table;
    }
    
    ##################
    ### 5 features ###
    ##################
    /**
     * Override this.
     * Return true if this table shall be able to be ordered by headers.
     */
	public function isOrdered() : bool { return true; }

	/**
	 * Override this.
	 * Return true if this table shall be searchable over all columns with one input field.
	 * This is called "HugeQuery" in the GDT_Table implementation.
	 * @return boolean
	 */
	public function isSearched() { return true; } # GDT$searchable

	/**
	 * Override this.
	 * Return true if you want to be able to filter your data by your header columns.
	 * @return boolean
	 */
	public function isFiltered() { return true; } # GDT#filterable

	/**
	 * Override this.
	 * Return true if you want pagination for this table method.
	 * @return boolean
	 */
	public function isPaginated() { return true; } # creates a GDT_Pagemenu
	
	/**
	 * Override this.
	 * Return true if you want to be able to sort this table data manually.
	 * This requires a GDT_Sort field in your GDO columns / headers as well as MethodSort endpoint.
	 * @return boolean
	 * @see GDT_Sort
	 * @see MethodSort
	 */
	public function isSorted() { return false; } # Uses js/ajax and GDO needs to have GDT_Sort column.
	
	############
	### CRUD ###
	############
	public function isCreateable(GDO_User $user) : bool { return false; }
	public function isReadable(GDO_User $user) : bool { return false; }
	public function isUpdateable(GDO_User $user) : bool { return false; }
	public function isDeleteable(GDO_User $user) : bool { return false; }
	
	public function getDefaultOrder() : ?string
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
	
	public function getIPP() : int
	{
		return $this->gdoParameterValue($this->getIPPName());
	}
	
	public function getPage() : int
	{
		return $this->gdoParameterValue($this->getPageName());
	}
	
	public function getSearchTerm() : string
	{
		if ($var = $this->gdoParameterVar($this->getSearchName()))
		{
			return $var;
		}
		return '';
	}
	
	public function getOrderTerm() : string
	{
		return $this->gdoParameterValue($this->getOrderName());
	}
	
	###############
	### Execute ###
	###############
	public function execute()
	{
		return GDT_Tuple::makeWith($this->renderTable());
	}
	
	public function validate() : bool
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
	
	public function getMethodTitle() : string
	{
		return $this->getTableTitle();
	}
	
	public function getTableTitleLangKey()
	{
	    return strtolower('list_'.$this->getModuleName().'_'.$this->getMethodName());
	}
	
	public function getTableTitle()
	{
		if (isset($this->table))
		{
		    $key = $this->getTableTitleLangKey();
		    return t($key, [$this->table->countItems()]);
		}
		else
		{
			$key = strtolower(sprintf('mt_%s_%s', $this->getModuleName(), $this->getMethodName()));
			return t($key);
		}
	}
	
	protected function setupTitle(GDT_Table $table)
	{
	    $table->titleRaw($this->getTableTitle());
	}
	
	protected function setupCollection(GDT_Table $table)
	{
	    $headers = $this->gdoHeaderCache();
	    $this->table->addHeaderFields(...$headers);
	    
	    # 5 features
	    if ($this->isOrdered())
	    {
		    $table->ordered($this->gdoParameter($this->getOrderName()));
	    }
	    $table->filtered($this->isFiltered());
	    $table->searched($this->isSearched());
	    $table->sorted($this->isSorted());
	    if ($this->isPaginated())
	    {
	    	$table->paginated(true, $table->href, $this->getIPP());
	    	$table->pagemenu->page($this->gdoParameterValue($this->getPageName()));
	    }
	    
	    # 4 editor permissions
	    $user = GDO_User::current();
	    $table->creatable($this->isCreateable($user));
	    $table->readable($this->isReadable($user));
	    $table->updatable($this->isUpdateable($user));
	    $table->deletable($this->isDeleteable($user));
	    
	    # 1 speedup
	    $table->fetchInto($this->useFetchInto());
	   
	}
	
	public function getTable() : GDT_Table
	{
		if (!isset($this->table))
		{
			$this->table = $this->createCollection();
		}
		return $this->table;
	}
	
// 	public function onInit()
// 	{
// // 		$this->initTable();
// 	}
	
	public function initTable()
	{
		$table = $this->getTable();
		$this->setupCollection($table);
		$this->onInitTable();
		$this->beforeCalculateTable($table);
		$this->calculateTable($table);
		$this->validate();
	    $this->calculateTable($table);
	    $result = $table->getResult();
        $result->table = $this->gdoFetchAs();
	    $this->setupTitle($table);
	    return $table;
	}
	
	public function renderTable()
	{
	    return $this->initTable();
	}
	
	protected function beforeCalculateTable(GDT_Table $table)
	{
		if ($this->isPaginated())
		{
			$result = $this->getResult();
			$this->table->pagemenu->pageName = $this->getPageName();
			$this->table->pagemenu->numItems(count($result->getData()));
		}
	}
	
	protected function calculateTable(GDT_Table $table)
	{
	    # Exec
	    $result = $this->getResult();
	    
	    # Exec features
	    if ($this->isFiltered())
	    {
	        $result = $result->filterResult($result->getFullData(), $this->gdoTable(), $table->getHeaderFields(), $table->headers->getName());
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
	
	public function renderCLIHelp() : string
	{
	    $this->calculateTable($this->initTable());
	    return parent::renderCLIHelp();
	}
	
}
