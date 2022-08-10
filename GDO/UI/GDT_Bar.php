<?php
namespace GDO\UI;


/**
 * A bar is a container without padding or margin.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
class GDT_Bar extends GDT_Container
{
	public bool $flex = true;
	public int $flexDirection = self::HORIZONTAL;
	public bool $flexWrap = true;
	
	protected function setupHTML(): void
	{
		$this->addClass('gdt-bar');
		$this->css('text-align', 'center');
		parent::setupHTML();
	}

}
