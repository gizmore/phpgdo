<?php
declare(strict_types=1);
namespace GDO\Table;

use GDO\Core\GDO_Exception;
use GDO\DB\ArrayResult;
use GDO\DB\Query;
use GDO\UI\GDT_DeleteButton;
use GDO\UI\GDT_EditButton;
use GDO\User\GDO_User;

/**
 * A method that displays a table via a query.
 *
 * @version 7.0.3
 * @since 3.0.0
 * @author gizmore
 * @see GDT_Table
 */
abstract class MethodQueryTable extends MethodTable
{

	public function gdoHeaders(): array
	{
		return array_merge($this->gdoButtonHeaders(), $this->gdoTable()->gdoColumnsCache());
	}

	protected function gdoButtonHeaders(): array
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

	protected function createCollection(): GDT_Table
	{
		$table = parent::createCollection();
		return $table->query($this->getQuery());
	}

	# ###############
	# ## Abstract ###
	# ###############

	/**
	 * Override this function to return a query for your table.
	 * Defaults to "select all" from your GDO table.
	 */
	public function getQuery(): Query
	{
		return $this->gdoTable()->select();
	}

	/**
	 * This method should not be called anymore when using Queried tables.
	 *
	 * {@inheritdoc}
	 * @see MethodTable::getResult
	 */
	public function getResult(): ArrayResult
	{
		throw new GDO_Exception('Shuld not return result for queried methods!');
	}


	# ###########
	# ## Exec ###
	# ###########

	protected function beforeCalculateTable(GDT_Table $table): void {}

	/**
	 * Calculate the GDT_Table object for queried tables.
	 */
	protected function calculateTable(GDT_Table $table): void
	{
		$query = $table->query;
		// $table->fetchAs($this->gdoFetchAs());

		if ($this->isSearched())
		{
			if ($s = $this->gdoParameter($this->getSearchName())->getVar())
			{
				foreach ($this->gdoHeaderCache() as $gdt)
				{
					$gdt->searchQuery($query, $s);
				}
			}
		}

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
			$table->countQuery($query->copy());
			$table->paginated(true, $this->getCurrentHREF(), $this->getIPP());
			$table->pagemenu->numItems($table->countItems());
			$table->pagemenu->pageName = $this->getPageName();
			$table->pagemenu->page($this->getPage());
			$table->pagemenu->paginateQuery($table->query);
		}
	}

}
