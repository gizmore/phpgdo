<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A label is not a field.
 * It has no values, only icon and label.
 * WithLabel renderings are called on render functions.
 *
 * @version 7.0.1
 * @since 6.1.0
 * @author gizmore
 * @see WithIcon
 * @see WithLabel
 */
class GDT_Label extends GDT
{

	use WithIcon;
	use WithLabel;

	public function renderCLI(): string { return $this->renderLabelText(); }

	public function renderXML(): string { return '<label>' . $this->renderLabelText() . '</label>'; }

	public function renderJSON(): array|string|null|int|bool|float { return $this->renderLabelText(); }

	public function renderHTML(): string { return "<label>{$this->renderLabelText()}</label>"; }

	public function renderCard(): string { return "<div class=\"gdt-card-label\">{$this->renderLabelText()}</div>"; }

	public function renderList(): string { return $this->renderLabelText(); }

}
