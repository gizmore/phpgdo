<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_Int;

/** A signed integer that rejects malformed raw form input before conversion. */
final class GDT_WholeNumber extends GDT_Int
{
    public function validate(int|float|string|array|null|object|bool $value): bool
    {
        $raw = $this->getVar() ?? $value;
        if ($raw !== null && (!is_int($raw) && (!is_string($raw) || !preg_match('/\A-?[0-9]+\z/', $raw))))
        {
            return $this->error('err_whole_integer');
        }
        return parent::validate($value);
    }
}
