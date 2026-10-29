<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_UInt;

/** An image or UI width in whole pixels. */
final class GDT_Width extends GDT_UInt
{
    public null|int|float $min = 1;
}
