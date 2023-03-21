<?php
namespace GDO\UI;

/**
 * Add a string html attribute.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @deprecated Use $var? Only used in GDT_HTML?
 *
 * @author gizmore
 */
trait WithHTML
{

	public string $html;

	public function html(string $html): self
	{
		$this->html = $html;
		return $this;
	}

}
