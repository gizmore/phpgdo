<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithValue;

/**
 * Very simple field that only has custom html content.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.7.0
 */
// final class GDT_HTML extends GDT_Container
final class GDT_HTML extends GDT
{
	use WithValue;
	
	public function renderHTML() : string { return $this->var; }
	
}
