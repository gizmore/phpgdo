<?php
namespace GDO\Table;

use GDO\DB\ArrayResult;
use GDO\DB\Query;
use GDO\User\GDO_User;
use GDO\UI\GDT_DeleteButton;
use GDO\UI\GDT_EditButton;
use GDO\Core\GDO_Exception;

/**
 * A method that displays a table via a query.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 * @see GDT_Table
 */
abstract class MethodQueryTable extends MethodTable
{

	public function gdoHeaders(): array
	{
		return array_merge($this->gdoButtonHeaders(), $this->gdoTable()->gdoColumnsCache());
	}

	protected function gdoButtonHeaders()
	{
		$user = GDO_User::current();
		$headers = [];
		if ($this->isDeleteable($user))
		{
			$headers[] = GDT_DeleteButton::make();
		}
		if ($this->isUpdateable($user))
		{
			$headers[] = GDT_EditButton::make();
		}
		return $headers;
	}

	public function createCollection(): GDT_Table
	{
		$table = parent::createCollection();
		return $table->query($this->getQuery());
	}

	# ###############
	# ## Abstract ###
	# ###############
	/**
	 * This method should not be called anymore when using Queried tables.
	 *
	 * {@inheritdoc}
	 * @see \GDO\Table\MethodTable::getResult()
	 */
	public function getResult(): ArrayResult
	{
		throw new GDO_Exception("Shuld not return result for queried methods!");
	}

	/**
	 * Override this function to return a query for your table.
	 * Defaults to select all from your GDO table.
	 */
	public function getQuery(): Query
	{
		return $this->gdoTable()->select();
	}

	/**
	 * Return a query to count items for pagination.
	 * Usually you can leave this to gdo, letting it transform your query above.
	 * But it's possible to return an own CountQuery.
	 */
	public function getCountQuery(): Query
	{
		return $this->getQuery();
	}

	# ###########
	# ## Exec ###
	# ###########
	protected function beforeCalculateTable(GDT_Table $table)
	{
	}

	/**
	 * Calculate the GDT_Table object for queried tables.
	 */
	protected function calculateTable(GDT_Table $table)
	{
		$query = $table->query;
		// $table->fetchAs($this->gdoFetchAs());

		if ($this->isFiltered())
		{
			$f = $this->getFilterField();
			foreach ($this->gdoHeaderCache() as $gdt)
			{
				$gdt->filterQuery($query, $f);
			}
		}

		if ($this->isOrdered())
		{
			# Get order with sanity check
			$order = $this->gdoParameter($this->getOrderName())
				->getVar();
			# order the query
			$query->order($order);
		}
		elseif ($defaultOrder = $this->getDefaultOrder())
		{
			$query->order($defaultOrder);
		}

		if ($this->isPaginated())
		{
			$table->countQuery($this->getCountQuery());
			$table->paginated(true, $this->getCurrentHREF(), $this->getIPP());
			$table->pagemenu->page($this->getPage());
			$table->pagemenu->numItems($table->countItems());
			$table->pagemenu->pageName = $this->getPageName();
			$table->pagemenu->paginateQuery($table->query);
		}
	}

}
