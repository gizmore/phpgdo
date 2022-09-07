<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;

/**
 * A response renders a GDT result.
 * If RENDER_WEBSITE mode, we let GDT_Page do it's job.
 * If you add a response to a response, it will just steal it's fields. (unwrap)
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
final class GDT_Response extends GDT_Tuple
{
	public function render() : string
	{
		switch (Application::$INSTANCE->mode)
		{
			case GDT::RENDER_WEBSITE:
				return $this->renderWebsite();
			case GDT::RENDER_XML:
				hdr('Content-Type: application/xml');
				return $this->renderXML();
			case GDT::RENDER_JSON:
				hdr('Content-Type: application/json');
				return json_encode($this->renderJSON(), GDO_JSON_DEBUG?JSON_PRETTY_PRINT:0); # pretty json
			default:
				return parent::render();
		}
	}
	
	/**
	 * HTML Render this response via GDT_Page
	 * @return string
	 */
	public function renderWebsite() : string
	{
		$content = $this->renderFields(GDT::RENDER_HTML);
		if (Application::$INSTANCE->isAjax())
		{
			return $content; # ajax is html without html boilerplate.
		}
		$page = GDT_Page::instance();
		return $page->html($content)->renderMode(GDT::RENDER_HTML);
	}
	
	public function code(int $code) : self
	{
		Application::$INSTANCE->setResponseCode($code);
		return $this;
	}
	
	public function hasError() : bool
	{
		return Application::isError();
	}
	
}
