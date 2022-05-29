<?php
namespace GDO\UI;

use GDO\Core\Debug;
use GDO\Core\Application;
use GDO\Core\GDO_Exception;

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
	public static function fromException(\Throwable $t) : self
	{
		return self::make()->exception($t);
	}
	
	public function exception(\Throwable $t) : self
	{
		$is_html = Application::$INSTANCE->isHTML();
		$this->titleRaw("Exception!");
		$this->textRaw(Debug::backtraceException($t, $is_html, $t->getMessage()));
		return $this;
	}
	
	public function renderHTML() : string
	{
		$this->addClass('gdt-error');
		hdrc('HTTP/1.1 ' . GDO_Exception::DEFAULT_ERROR_CODE  .' GDO Error');
// 		hdr('X-GDO-ERROR: ' . $this->renderText());
		return parent::renderHTML();
	}
	
}
