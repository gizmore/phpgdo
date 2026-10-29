<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_UInt;

/** An image or UI height in whole pixels. */
final class GDT_Height extends GDT_UInt
{
    public null|int|float $min = 1;
}
