<?php
namespace GDO\Table;

use GDO\Core\GDT_UInt;

/**
 * Items per page for headers.
 * Defaults to Module_Table->cfgIPP() (cli and http variants exist)
 *
 * @version 7.0.0
 * @since 6.1.0
 * @author gizmore
 */
final class GDT_IPP extends GDT_UInt
{

	#############
	### Field ###
	#############
	protected function __construct()
	{
		parent::__construct();
		$this->initial($this->getDefaultIPP());
		$this->min = 1;
		$this->max = 1000;
		$this->bytes = 2;
	}

	public function getDefaultIPP(): int
	{
		return Module_Table::instance()->cfgItemsPerPage();
	}

	public function getDefaultName(): string
	{
		return 'ipp';
	}

	################
	### Features ###
	################

	public function defaultLabel(): self
	{
		return $this->label('ipp');
	}

	public function isHidden(): bool { return true; }

	public function isCLIHidden(): bool { return true; }

	public function isOrderable(): bool { return false; }

	public function isSearchable(): bool { return false; }

	public function isFilterable(): bool { return false; }

	###########
	### GDT ###
	###########

	public function isSerializable(): bool { return false; }

}
