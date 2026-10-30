<?php
declare(strict_types=1);

namespace GDO\UI\Test;

use GDO\UI\GDT_Height;
use GDO\UI\GDT_Width;
use GDO\UI\GDT_WholeNumber;
use GDO\Tests\TestCase;

final class PixelDimensionsTest extends TestCase
{
    public function testDimensionsAcceptOnlyPositiveWholePixels(): void
    {
        foreach ([GDT_Width::class, GDT_Height::class] as $type)
        {
            self::assertNotNull($type::make('dimension')->var('128')->validated());
            self::assertNull($type::make('dimension')->var('1.5')->validated());
            self::assertNull($type::make('dimension')->var('2abc')->validated());
            self::assertNull($type::make('dimension')->var('0')->validated());
            self::assertNull($type::make('dimension')->var('-1')->validated());
            self::assertNull($type::make('dimension')->max(128)->var('129')->validated());
            self::assertNotNull($type::make('dimension')->var(null)->validated());
        }
    }

    public function testSignedWholeNumberRejectsFractionalAndMalformedValues(): void
    {
        foreach (['-4', '0', '5'] as $value)
            self::assertNotNull(GDT_WholeNumber::make('offset')->var($value)->validated());
        foreach (['1.5', '-2.5', '3abc'] as $value)
            self::assertNull(GDT_WholeNumber::make('offset')->var($value)->validated());
        self::assertNotNull(GDT_WholeNumber::make('offset')->var(null)->validated());
    }

    public function testInvalidInputHasGermanErrorText(): void
    {
        $this->lang('de');
        try
        {
            $width = GDT_Width::make('width')->var('1.5');
            $offset = GDT_WholeNumber::make('offset')->var('1.5');
            self::assertNull($width->validated());
            self::assertNull($offset->validated());
            self::assertSame('Bitte eine ganze Pixelzahl eingeben.', $width->renderError());
            self::assertSame('Bitte eine ganze Zahl eingeben.', $offset->renderError());
        }
        finally { $this->lang('en'); }
    }
}
