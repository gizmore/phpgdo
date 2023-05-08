<?php
namespace GDO\UI;

/**
 * A success is a panel with a special css class and icon.
 *
 * @author gizmore
 * @see GDT_Error
 */
final class GDT_Success extends GDT_Panel
{

	public int $code = 200;

	############
	### Code ###
	############

	protected function __construct()
	{
		parent::__construct();
		$this->addClass('gdt-success');
		$this->addClass('alert');
		$this->addClass('alert-success');
		$this->icon = 'check';
	}

	public function code(int $code): self
	{
		$this->code = $code;
		return $this;
	}

	##############
	### Render ###
	##############
	public function renderCLI(): string
	{
		return Color::green($this->renderText()) . "\n";
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
		return ['message' => $this->renderText()];
	}

}
