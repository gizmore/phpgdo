<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO_Exception;
use Throwable;

/**
 * An error is a message box with a special css class.
 * It can be configured via an exception.
 *
 * @version 7.0.3
 * @since 3.0.0
 * @author gizmore
 */
final class GDT_Error extends GDT_Panel
{

	public int $code = GDO_Exception::GDT_ERROR_CODE;

	############
	### Code ###
	############

	protected function __construct()
	{
		parent::__construct();
		$this->icon = 'error';
		$this->addClass('gdt-error');
		$this->addClass('alert');
		$this->addClass('alert-danger');
	}

	###########
	### GDT ###
	###########

	public function code(int $code): self
	{
		$this->code = $code;
		return $this;
	}

	public function fromException(Throwable $t): self
	{
		$is_html = Application::$INSTANCE->isHTML();
		$this->title('exception');
		$this->textRaw(Debug::backtraceException($t, $is_html, $t->getMessage()));
		Application::setResponseCode($t->getCode());
		return $this;
	}

	public function renderHTML(): string
	{
		hdrc('HTTP/1.1 ' . $this->code . ' GDT_Error');
		hdr('X-GDO-ERROR: ' . str_replace(["\r", "\n"], ' - ', $this->renderText()));
		return parent::renderHTML();
	}

	public function renderCLI(): string
	{
		return Color::red($this->renderText()) . "\n";
	}

}
