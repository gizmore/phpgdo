<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\CLI\CLI;
use GDO\UI\GDT_Page;

/**
 * A response renders a GDT result.
 * If RENDER_WEBSITE mode, we let GDT_Page do it's job.
 * If you add a response to a response, it will just steal it's fields. (unwrap)
 *
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 */
final class GDT_Response extends GDT_Tuple
{

	public function render(): array|string|null
	{
        global $me;
		switch (Application::$MODE)
		{
			case GDT::RENDER_BINARY:
				return $this->renderFields(Application::$MODE);
			case GDT::RENDER_WEBSITE:
				return $this->renderWebsite();
			case GDT::RENDER_XML:
				hdr('Content-Type: application/xml');
				return $this->renderXML();
			case GDT::RENDER_JSON:
				hdr('Content-Type: application/json');
                $code = Application::$RESPONSE_CODE;
                $status = $me->getModule()->gdoHumanName();
                $status = urlencode($status);
                hdr("HTTP/1.1 {$code} {$status}");
				$this->addFields(...GDT_Page::instance()->topResponse()->getAllFields());
				return json($this->renderJSON());
			case GDT::RENDER_CLI:
				return CLI::getTopResponse() . $this->renderCLI();
            case GDT::RENDER_PDF:
                return $this->renderPDF();
			default:
				return parent::render();
		}
	}

	/**
	 * HTML Render this response via GDT_Page
	 */
	public function renderWebsite(): string
	{
		$content = $this->renderFields(GDT::RENDER_HTML);
		if (Application::$INSTANCE->isAjax())
		{
			return $content; # ajax is html without html boilerplate.
		}
		$page = GDT_Page::instance();
		return $page->html($content)->renderMode(GDT::RENDER_HTML);
	}

    public function renderPDF(): string
    {
        $content = $this->renderFields(GDT::RENDER_HTML);
        $page = GDT_Page::instance();
        return $page->html($content)->renderMode(GDT::RENDER_PDF);
    }

	public function hasError(): bool
	{
		return Application::isError() ||
			parent::hasError();
	}

	public function code(int $code): self
	{
		Application::setResponseCode($code);
		return $this;
	}

}
