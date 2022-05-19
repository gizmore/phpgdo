<?php
namespace GDO\UI;

use GDO\Core\Debug;
use GDO\Core\Application;

/**
 * An error is a message box with a special css class.
 * It can be configured via an exception.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.0
 */
final class GDT_Error extends GDT_MessageBox
{
	public function exception(\Throwable $t) : self
	{
		$is_html = Application::instance()->isHTML();
		$this->titleRaw("Exception!");
		$this->textRaw(Debug::backtraceException($t, $is_html, $t->getMessage()));
		return $this;
	}
	
}
