<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;

/**
 * A navigation tab menu with tabs.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore
 */
final class GDT_Tabs extends GDT
{

	use WithFields;

	public function getDefaultName(): string
	{
		return 'tabs';
	}

// 	/**
// 	 * @var GDT_Tab[]
// 	 */
// 	private $tabs = [];
	/**
	 * @return GDT_Tab[]
	 */
	public function getTabs(): array
	{
		return $this->getFields();
	}

	public function addTab(GDT_Tab $tab): self
	{
		return $this->addField($tab);
// 		$this->tabs[] = $tab;
// 		return $this;
	}

// 	public function getFields() : array
// 	{
// 		return $this->tabs;
// 	}

	##############
	### Render ###
	##############
	public function renderForm(): string
	{
		return GDT_Template::php('UI', 'tabs_html.php', [
			'field' => $this, 'cell' => false]);
	}

	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'tabs_html.php', [
			'field' => $this, 'cell' => true]);
	}

}
