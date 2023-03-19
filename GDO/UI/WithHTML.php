<?php
namespace GDO\UI;

/**
 * Add a string html attribute.
 * 
 * @deprecated Use $var? Only used in GDT_HTML?
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
trait WithHTML
{
	public string $html;
	
	public function html(string $html): static
	{
		$this->html = $html;
		return $this;
	}
	
}
