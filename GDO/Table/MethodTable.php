<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\ArrayResult;
use GDO\Core\GDT;
use GDO\Core\GDT_Fields;
use GDO\Core\GDO;
use GDO\User\GDO_User;
use GDO\UI\GDT_SearchField;

/**
 * A method that displays a table from memory via ArrayResult.
 * It's the base class for more complex methods like MethodQueryTable or MethodQueryCards.
 * The basic API is identical for static memory results and queried data.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 * @see ArrayResult
 * @see GDT_Table
 * @see GDT
 * @see GDO
 */
abstract class MethodTable extends Method
{
	public GDT_Table $table;
	
	/**
	 * @return GDT[string]
	 */
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			$this->addComposeParameters($this->gdoParameters());
			$this->withAppliedInputs($this->getInputs());
			$this->addComposeParameters($this->gdoTableFeatures());
		}
		return $this->parameterCache;
	}
	
	private function gdoTableFeatures() : array
	{
		$features = [];
		$table = $this->getTable();
		if ($this->isPaginated())
		{
			$features[] = $table->paginated(true, $this->getDefaultIPP())->pagemenu->getIPPField();
			$features[] = $table->pagemenu->getPageNumField();
			$table->pagemenu->getPageNumField()->table($table);
		}
		if ($this->isSearched())
		{
			$features[] = GDT_SearchField::make('search')->gdo($this->gdoTable());
		}
		if ($this->isOrdered())
		{
			$order = $table->ordered(true, $this->getDefaultOrder())->order;
			$features[] = $order;
			$order->gdo($this->gdoTable());
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
//     public function gdoTableName() { return 'table'; }
    public function gdoFetchAs() { return $this->gdoTable(); }
    
    /**
     * Override this with returning an ArrayResult with data.
     * @return ArrayResult
     */
    public function getResult() { return new ArrayResult([], $this->gdoTable()); }

    /**
     * Override this, if it is required to fetch a different class than your gdoTable().
     * @return GDO
     */
    public function fetchAs() {}

    /**
     * Override this to toggle fetchInto speedup in table rendering to reduce GDO allocations.
     * @return boolean
     */
    public function useFetchInto() { return false; }
    
    /**
     * Default IPP defaults to config in Module_Table.
     * @see Module_Table::getConfig()
     * @return string
     */
    public function getDefaultIPP() { return Module_Table::instance()->cfgItemsPerPage(); }
    
    /**
     * Override this.
     * Return an array of GDT[] for the table headers.
     * Defaults to all fields from your gdoTable(). 
     * @return GDT_Fields
     */
    public function gdoHeaders() { return $this->gdoTable()->gdoColumnsCache(); }
    
    /**
     * The header GDT name.
     * Defaults to 'o' for get parameters.
     * You need to adjust this when showing multiple tables or methods in a single page.
     * @return string
     */
//     public function getHeaderName() { return 'o'; }
    
    /**
     * Override this.
     * Called upon creation of the GDT_Table.
     * @param GDT_Table $table
     */
    public function onCreateTable(GDT_Table $table) : void {}
    
    /**
     * Creates the collection GDT.
     * @return GDT_Table|GDT_List
     */
    public function createCollection()
    {
        $this->table = GDT_Table::make();
        $this->onCreateTable($this->table);
//         $this->table->headerName($this->getHeaderName());
        return $this->table;
//         return $this->table->gdtTable($this->gdoTable());
    }
    
    ##################
    ### 5 features ###
    ##################
    /**
     * Override this.
     * Return true if this table shall be able to be ordered by headers.
     * @return boolean
     */
	public function isOrdered() { return true; }

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
	
	###
	public function getDefaultOrder()
	{
	    foreach ($this->table->getHeaderFields() as $gdt)
	    {
	        if ($gdt->isOrderable())
	        {
	            return $gdt->name . ($gdt->isOrderDefaultAsc() ? ' ASC' : ' DESC');
	        }
	    }
	}
	
	public function getIPP()
	{
		return $this->gdoParameterValue('ipp');
	}
	
	public function getPage()
	{
		return $this->gdoParameterValue('page');
	}
	
	public function getSearchTerm()
	{
		return $this->gdoParameterValue('search');
	}
	
	public function getOrderTerm()
	{
		return $this->gdoParameterValue('order');
	}
	
	###############
	### Execute ###
	###############
	public function execute()
	{
		return $this->renderTable();
	}
	
	public function validate() : bool
	{
		$valid = true;
		foreach ($this->gdoParameterCache() as $gdt)
		{
			if (!$gdt->validateInput($this->getInput($gdt->getName())))
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
	    $key = $this->getTableTitleLangKey();
	    return t($key, [$this->table->countItems()]);
	}
	
	protected function setupTitle(GDT_Table $table)
	{
	    $table->titleRaw($this->getTableTitle());
	}
	
	protected function setupCollection(GDT_Table $table)
	{
	    $table->gdo($this->gdoTable());
	    
	    # 5 features
	    $table->ordered($this->isOrdered(), $this->getDefaultOrder());
	    $table->filtered($this->isFiltered());
	    $table->searched($this->isSearched());
	    $table->sorted($this->isSorted());
	    if ($this->isPaginated())
	    {
	    	$table->paginated($this->isPaginated(), null, $this->getIPP());
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
	
	public static function make() : self
	{
		$obj = parent::make();
// 		$obj->onInitTable();
		return $obj;
	}
	
	public function getTable() : GDT_Table
	{
		if (!isset($this->table))
		{
			$this->table = $this->createCollection();
// 			$this->table->setupHeaders($this->isSearched(), $this->isPaginated());
			$this->table->addHeaders(...$this->gdoHeaders());
// 			$this->setupCollection($this->table);
		}
		return $this->table;
	}
	
	public function initTable()
	{
// 		$this->onInitTable();
// 		$this->onInit();
		$table = $this->getTable();
		$this->setupCollection($this->table);
		$this->table->result = null;
		$this->beforeCalculateTable($table);
		$this->validate();
// 	    $this->createTable($table);
	    $this->calculateTable($table);
	    $result = $table->getResult();
	    if ($fetchAs = $this->fetchAs())
	    {
	        $table->fetchAs($fetchAs);
	        $result->table = $fetchAs;
	    }
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
			$this->table->pagemenu->items(count($result->getData()));
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
	        $result = $table->multisort($result, $this->getDefaultOrder());
	    }
	    if ($this->isPaginated())
	    {
	        $result = $this->table->pagemenu->paginateResult($result, $this->getPage(), $this->getIPP());
	    }
	    $table->result($result);
	}
	
	public function renderCLIHelp() : string
	{
	    $this->calculateTable($this->initTable());
	    return parent::renderCLIHelp();
	}
	
}
