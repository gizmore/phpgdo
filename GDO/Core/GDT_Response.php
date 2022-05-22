<?php
namespace GDO\Core;

use GDO\UI\WithText;
use GDO\UI\GDT_Page;

/**
 * A response renders a GDT result.
 * in RENDER_HTML mode, we let GDT_Page do it's job.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
final class GDT_Response extends GDT
{
	use WithText;
	use WithFields;
	
	public function render() : string
	{
		switch (Application::instance()->mode)
		{
			case GDT::RENDER_HTML:
				return $this->renderPage();
			default:
				return parent::render();
		}
	}
	
	/**
	 * HTML Render this response via GDT_Page
	 * @return string
	 */
	public function renderPage()
	{
		$content = parent::renderHTML();
		$page = GDT_Page::instance();
		return $page->html($content)->render();
	}
	
}
