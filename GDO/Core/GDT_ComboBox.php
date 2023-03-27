<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Table\GDT_Filter;

/**
 * A combobox is a string with additional completion and dropdown.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 * @see GDT_Select
 */
class GDT_ComboBox extends GDT_String
{

	use WithCompletion;

	###############
	### Choices ###
	###############
	public array $choices;

	public function choices(array $choices): self
	{
		$this->choices = $choices;
		return $this;
	}

	############
	### JSON ###
	############
	public function configJSON(): array
	{
		$var = $this->getVar();
		return array_merge(parent::configJSON(), [
			'combobox' => 1,
			'selected' => [
				'id' => $var,
				'text' => $var,
				'display' => $this->renderOption(),
			],
			'completionHref' => $this->completionHref ?? null,
		]);
	}

	##############
	### Render ###
	##############
	public function renderFilter(GDT_Filter $f): string
	{
		if (isset($this->completionHref))
		{
			return GDT_Template::php('Form', 'combobox_form.php', ['field' => $this, 'f' => $f]);
		}
		else
		{
			return parent::renderFilter($f);
		}
	}

	public function renderForm(): string
	{
		if (isset($this->completionHref))
		{
			$tVars = [
				'field' => $this,
			];
			return GDT_Template::php('Core', 'combobox_form.php', $tVars);
		}
		return parent::renderForm();
	}

}
