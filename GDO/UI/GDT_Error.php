<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO_Exception;
use GDO\Util\Strings;
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

	public static function fromException(Throwable $t): self
	{
		$is_html = Application::$INSTANCE->isHTML();
		$error = self::make()->title('exception', [$t->getMessage()]);
		$error->textRaw(Debug::backtraceException($t, $is_html, $t->getMessage()));
		if (!($t instanceof GDO_Exception))
		{
			Application::setResponseCode(500);
		}
		return $error;
	}

	public function renderHTML(): string
	{
		hdr("X-GDO-ERROR: {$this->renderHeaderText()}");
		return parent::renderHTML();
	}

	public function renderHeaderText(): string
	{
		return self::displayHeaderText($this->renderTitle());
	}

	public static function displayHeaderText(string $text): string
	{
		$text = $text ? str_replace(["\r", "\n"], ' | ', $text) : t('no_information');
		$text = CLI::removeColorCodes($text);
		return Strings::dotted($text, 1024);
	}

	public function renderCLI(): string
	{
		return Color::red($this->renderText()) . "\n";
	}

}
