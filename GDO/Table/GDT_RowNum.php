<?php
namespace GDO\Table;

use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Template;

/**
 * Can be first element in a @link GDO_Table to show checkmark selection.
 * Table header is select-all-tristate.
 *
 * @author gizmore
 */
final class GDT_RowNum extends GDT_Checkbox
{

	public int $num = 0;

	public bool $multiple = true;
	public bool $toggleAll = false;

	public function isOrderable(): bool { return false; }

	public function getDefaultName(): ?string { return 'rbx'; }

	public function renderTHead(): string
	{
		return GDT_Template::php('Table', 'rownum_filter.php', ['field' => $this]);
	}

	###############################
	### Different filter header ###
	###############################

	public function renderHTML(): string
	{
		return GDT_Template::php('Table', 'rownum_html.php', ['field' => $this]);
	}

	public function toggleAll($toggleAll)
	{
		$this->toggleAll = $toggleAll;
		return $this;
	}

	public function displayHeaderLabel() { return ''; }

}
