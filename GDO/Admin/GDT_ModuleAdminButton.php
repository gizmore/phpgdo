<?php
namespace GDO\Admin;

use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\UI\GDT_Button;

/**
 * Admin section button. Only visible if module has href_admin.
 *
 * @version 7.0.1
 * @since 5.0.1
 * @author gizmore
 * @see GDT_Button
 * @see Module_Admin
 */
class GDT_ModuleAdminButton extends GDT_Button
{

	public function renderCell(): string
	{
		return $this->renderHTML();
	}

	public function renderHTML(): string
	{
		if ($module = $this->getConfigModule())
		{
			if ($href = $module->href_administrate_module())
			{
				$this->href($href);
				return parent::renderHTML();
			}
		}
		return GDT::EMPTY_STRING;
	}

	public function getConfigModule(): ?GDO_Module
	{
		return isset($this->gdo) ? $this->getRealModule() : null;
	}

	private function getRealModule(): GDO_Module
	{
		return ModuleLoader::instance()->getModule($this->gdo->getName());
	}

}
