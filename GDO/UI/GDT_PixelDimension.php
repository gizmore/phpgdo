<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_UInt;

/** A positive integer pixel dimension; null remains valid unless required. */
abstract class GDT_PixelDimension extends GDT_UInt
{
    public null|int|float $min = 1;

    public function validate(int|float|string|array|null|object|bool $value): bool
    {
        $raw = $this->getVar() ?? $value;
        if ($raw !== null && (!is_int($raw) && (!is_string($raw) || !preg_match('/\A[0-9]+\z/', $raw))))
        {
            return $this->error('err_pixel_integer');
        }
        return parent::validate($value);
    }
}
