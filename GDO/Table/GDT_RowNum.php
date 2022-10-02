<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_Checkbox;

/**
 * Can be first element in a @link GDO_Table to show checkmark selection.
 * Table header is select-all-tristate.
 * @author gizmore
 */
final class GDT_RowNum extends GDT_Checkbox
{
	public int $num = 0;

	public bool $multiple = true;
	
	public function isOrderable() : bool { return false; }
	
	public function getDefaultName() : ?string { return 'rbx'; }
	
	public bool $toggleAll = false;
	public function toggleAll($toggleAll)
	{
		$this->toggleAll = $toggleAll;
		return $this;
	}
	
	###############################
	### Different filter header ###
	###############################
	public function displayHeaderLabel() { return ''; }

	public function renderTHead() : string
	{
		return GDT_Template::php('Table', 'rownum_filter.php', ['field' => $this]);
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('Table', 'rownum_html.php', ['field' => $this]);
	}
	
}
