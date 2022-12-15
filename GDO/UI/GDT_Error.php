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
 * @version 7.0.1
 * @since 3.0.0
 */
final class GDT_Error extends GDT_Panel
{
	public static function fromException(\Throwable $t) : self
	{
		return self::make()->exception($t)->code(500);
	}
	
	############
	### Code ###
	############
	public int $code = GDO_Exception::DEFAULT_ERROR_CODE;
	public function code(int $code) : self
	{
		$this->code = $code;
		return $this;
	}

	###########
	### GDT ###
	###########
	protected function __construct()
	{
		parent::__construct();
		$this->icon = 'error';
		$this->addClass('gdt-error');
		$this->addClass('alert');
		$this->addClass('alert-danger');
	}
	
	public function exception(\Throwable $t) : self
	{
		$is_html = Application::$INSTANCE->isHTML();
		$this->title("exception");
		$this->textRaw(Debug::backtraceException($t, $is_html, $t->getMessage()));
		Application::setResponseCode($this->code);
		return $this;
	}
	
	public function renderHTML() : string
	{
		hdrc('HTTP/1.1 ' . $this->code . ' GDO Error');
		hdr('X-GDO-ERROR: ' . str_replace(["\r", "\n"], ' - ', $this->renderText()));
		return parent::renderHTML();
	}
	
	public function renderCLI() : string
	{
		return Color::red($this->renderText()) . "\n";
	}
	
}
