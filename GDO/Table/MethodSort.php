<?php
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT_Object;
use GDO\Core\MethodAjax;

/**
 * Generic ajax adapter that swaps two items using their GDT_Sort column.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
abstract class MethodSort extends MethodAjax
{
	/**
	 * The GDO table to be sortable.
	 * @return GDO
	 */
	public abstract function gdoSortObjects();
	
	/**
	 * Override this method with a permission check for a GDO.
	 * @param GDO $gdo
	 * @return boolean
	 */
	public function canSort(GDO $gdo) { return true; }
	
	public function gdoParameters() : array
	{
	    $table = $this->gdoSortObjects();
	    return [
	        GDT_Object::make('a')->notNull()->table($table),
	        GDT_Object::make('b')->notNull()->table($table),
	    ];
	}
	
	############
	### Exec ###
	############
	/**
	 * Method is ajax and always a write / transaction.
	 * {@inheritDoc}
	 * @see Method::isAlwaysTransactional()
	 */
	public function isAlwaysTransactional() : bool { return true; }

	/**
	 * Force ajax and JSON rendering.
	 * {@inheritDoc}
	 * @see Method::isAjax()
	 */
	public function isAjax() : bool { return true; }
	
	/**
	 * Find the sort column name and swap item sorting.
	 * {@inheritDoc}
	 * @see Method::execute()
	 */
	public function execute()
	{
	    # Compatibility check
		$table = $this->gdoSortObjects();
		if (!($name = $this->getSortingColumnName($table)))
		{
			return $this->error('err_table_not_sortable', [$table->gdoHumanName()]);
		}
		
		# Existance check
		$a = $this->gdoParameterValue('a');
		$b = $this->gdoParameterValue('b');
		
		# Permission check
		if ( (!$this->canSort($a)) || (!$this->canSort($b)) )
		{
		    return $this->error('err_table_not_sortable', [$table->gdoHumanName()]);
		}
		
		$sortA = $a->gdoVar($name);
		$sortB = $b->gdoVar($name);
		
		$a->saveVar($name, $sortB);
		$b->saveVar($name, $sortA);
		
		return $this->message('msg_sort_success');
	}
	
	###################
	### Sort Column ###
	###################
	/**
	 * Determine the sort column.
	 */
	protected function getSortingColumn(GDO $table) : ?GDT
	{
	    return $table->gdoColumnOf(GDT_Sort::class);
	}
	
	/**
	 * Get the name of the table's sort column.
	 */
	protected function getSortingColumnName(GDO $table) : ?string
	{
	    if ($gdt = $this->getSortingColumn($table))
	    {
	        return $gdt->name;
	    }
	    return null;
	}

	###########
	### SEO ###
	###########
	public function getMethodTitle() : string
	{
		return t('mt_sort', [$this->gdoSortObjects()->gdoHumanName()]);
	}
	
}
