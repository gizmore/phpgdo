<?php
namespace GDO\Cronjob;

use GDO\Core\GDT_String;

/**
 * A run-at syntax for cronjob entries.
 *
 * @since 6.11.1
 */
final class GDT_RunAt extends GDT_String
{

	public string $pattern = "#([*/0-9,]+\\s*){5}#";

}
