<?php
declare(strict_types=1);

namespace GDO\UI\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_PageBar;

final class PageBarTest extends TestCase
{
    public function testValidatesNamesBeforeResolvingBars(): void
    {
        $page = GDT_Page::instance();
        foreach (['left' => $page->leftBar(), 'right' => $page->rightBar(), 'bottom' => $page->bottomBar()] as $name => $bar)
        {
            $field = GDT_PageBar::make('position')->notNull()->var($name);
            self::assertNotNull($field->validated(), $name);
            self::assertSame($bar, $field->getValue(), $name);
        }
        self::assertNotNull(GDT_PageBar::make('position')->notNull()->var('none')->validated());
        self::assertNull(GDT_PageBar::make('position')->notNull()->var('sideways')->validated());
        self::assertNull(GDT_PageBar::make('position')->notNull()->var(null)->validated());
        self::assertNotNull(GDT_PageBar::make('position')->var(null)->validated());
    }
}
