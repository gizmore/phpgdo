<?php
namespace GDO\UI;

/**
 * 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.1
 */
class GDT_Box extends GDT_Container
{
	public bool $flex = true;
	public int $flexDirection = self::HORIZONTAL;
	public bool $flexWrap = true;
	public bool $flexShrink = false;
	
	protected function setupHTML(): void
	{
		$this->addClass('gdt-box');
		parent::setupHTML();
	}
	
}
