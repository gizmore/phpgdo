<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;
use GDO\Util\Strings;

/**
 * A response renders a GDT result.
 * If RENDER_HTML mode, we let GDT_Page do it's job.
 * If you add a response to a response, it will just steal it's fields. (unwrap)
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
final class GDT_Response extends GDT_Tuple
{
// 	use WithInstance;
	
	public static function instanceWith(GDT...$gdts) : self
	{
		$instance = self::instance();
		return $instance->addFields(...$gdts);
	}
	
	public function render() : string
	{
		switch (Application::$INSTANCE->mode)
		{
			case GDT::RENDER_HTML:
				return Strings::shrinkHTML($this->renderPage());
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
	public function renderPage() : string
	{
		$content = $this->renderFields(GDT::RENDER_CELL);
		if (Application::$INSTANCE->isAjax())
		{
			return $content; # ajax is html without html boilerplate.
		}
		$page = GDT_Page::instance();
		return $page->html($content)->renderHTML();
	}
	
	public function code(int $code) : self
	{
		Application::$INSTANCE->setResponseCode($code);
		return $this;
	}
	
}
