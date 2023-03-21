<?php
namespace GDO\Admin;

use GDO\Core\GDO_Module;
use GDO\Core\WithGDO;
use GDO\UI\GDT_Link;

/**
 * Is searchable, filterable, orderarble because it's the modulename.
 *
 * @author gizmore
 */
final class GDT_ModuleNameLink extends GDT_Link
{

	use WithGDO;

	public function isTestable(): bool { return false; }

	public function isOrderable(): bool { return true; }

	public function isSearchable(): bool { return true; }

	public function isFilterable(): bool { return true; }

	public function htmlClass(): string
	{
		return ' gdt-link';
	}

	public function renderCell(): string
	{
		return $this->renderHTML();
	}

	public function renderHTML(): string
	{
		$name = $this->getModuleLinked()->getName();
		$this->textRaw($name);
		$this->href(href('Admin', 'Configure', "&module=$name"));
		return parent::renderHTML();
	}

	public function getModuleLinked(): GDO_Module
	{
		return $this->gdo;
	}

}
