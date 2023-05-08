<?php
namespace GDO\UI\Test;

use GDO\Core\GDT;
use GDO\Tests\TestCase;
use GDO\UI\GDT_Label;
use GDO\UI\GDT_Page;
use function PHPUnit\Framework\assertStringContainsString;

final class UITest extends TestCase
{

	public function testSimpleLabel()
	{
		$label = GDT_Label::make()->labelRaw('teyst');
		$result = $label->renderMode(GDT::RENDER_HTML);
		assertStringContainsString('teyst', $result, 'Test if basic rendering works.');
		assertStringContainsString('<label', $result, 'Test if basic html rendering works.');
	}

	/**
	 * This method is a fine example of the GDOv7 philosophy.
	 * We render an empty page and check if the most bottom javascript preamble contains the GDO_REVISION as JS code.
	 * If this is there, the empty page has been rendered.
	 * And now i introduce generic rendering tests... all this is obsolete!
	 */
	public function testHTMLPageRendering()
	{
		$content = GDT_Page::instance()->renderHTML();
		assertStringContainsString('window.GDO_REVISION', $content, 'Test if page rendering works.');
		self::assertEquals(1, substr_count($content, 'window.GDO_REVISION'), 'Test if Javascript is only rendered once!');
	}

}
