<?php
namespace GDO\UI;

/**
 * A success is a panel with a special css class and icon.
 * 
 * @author gizmore
 */
final class GDT_Success extends GDT_Panel
{
	protected function __construct()
	{
		parent::__construct();
		$this->addClass('gdt-success');
		$this->icon = 'check';
	}
	
	############
	### Code ###
	############
	public int $code = 200;
	public function code(int $code) : self
	{
		$this->code = $code;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		return Color::green($this->renderText()) . "\n";
	}
	
}
