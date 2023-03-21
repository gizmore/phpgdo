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
 * @version 7.0.0
 * @since 6.3.0
 * @author gizmore
 */
class GDT_Sort extends GDT_UInt
{

	protected function __construct()
	{
		parent::__construct();
		$this->min = 0;
		$this->max = 65535;
		$this->bytes = 2;
		$this->notNull();
		$this->initial('0');
	}

	public function defaultLabel(): self { return $this->label('sorting'); }

	public function gdoAfterCreate(GDO $gdo): void
	{
		$gdo->saveVar($this->name, $gdo->getID());
	}

}
