<?php
namespace GDO\Table;

use GDO\Core\GDO;
use GDO\Core\GDT_UInt;

/**
 * This GDT makes a GDO table sortable.
 * Saves initial sorting with autoinc value.
 * 
 * @TODO on GDO with non auto-increment this will crash.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.3.0
 */
class GDT_Sort extends GDT_UInt
{
    public function defaultLabel(): static { return $this->label('sorting'); }
    
	protected function __construct()
	{
	    parent::__construct();
		$this->min = 0;
		$this->max = 65535;
		$this->bytes = 2;
		$this->notNull();
		$this->initial('0');
	}
	
	public function gdoAfterCreate(GDO $gdo) : void
	{
		$gdo->saveVar($this->name, $gdo->getID());
	}

}
