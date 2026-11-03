<?php
declare(strict_types=1);

namespace GDO\UI\Test;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Language\Trans;
use PHPUnit\Framework\TestCase;

final class PlaceholderTest extends TestCase
{
    private int $previousMode;

    protected function setUp(): void
    {
        $this->previousMode = Application::$MODE;
        Application::$MODE = GDT::RENDER_WEBSITE;
    }

    protected function tearDown(): void
    {
        Application::$MODE = $this->previousMode;
    }

    public function testRawPlaceholderIsEscapedAndCanBeRemoved(): void
    {
        $field = GDT_String::make('example');
        self::assertSame('', $field->htmlPlaceholder());
        self::assertSame(' placeholder="A &quot;quote&quot; &amp; &lt;tag&gt;"',
            $field->placeholderRaw('A "quote" & <tag>')->htmlPlaceholder());
        self::assertSame('', $field->placeholderNone()->htmlPlaceholder());
    }

    public function testTranslatedPlaceholderAndReplacement(): void
    {
        $old = Trans::$CACHE[Trans::$ISO] ?? null;
        Trans::$CACHE[Trans::$ISO]['test_placeholder'] = 'Hello %s';
        try
        {
            $field = GDT_String::make('example')->placeholder('test_placeholder', ['<friend>']);
            self::assertSame(' placeholder="Hello &lt;friend&gt;"', $field->htmlPlaceholder());
            self::assertSame(' placeholder="Raw"', $field->placeholderRaw('Raw')->htmlPlaceholder());
            self::assertSame(' placeholder="Hello %s"', $field->placeholder('test_placeholder')->htmlPlaceholder());
            self::assertSame('', $field->placeholderNone()->htmlPlaceholder());
        }
        finally
        {
            if ($old === null) { unset(Trans::$CACHE[Trans::$ISO]); }
            else { Trans::$CACHE[Trans::$ISO] = $old; }
        }
    }
}
