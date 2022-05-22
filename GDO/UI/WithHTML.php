<?php
namespace GDO\UI;

/**
 * Add a string html attribute.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
trait WithHTML
{
	public string $html;
	public function html(string $html) : self
	{
		$this->html = $html;
		return $this;
	}
	
}
