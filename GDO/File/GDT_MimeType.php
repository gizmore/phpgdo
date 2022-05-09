<?php
namespace GDO\File;

use GDO\Core\GDT_String;

/**
 * Mime Filetype widget.
 * Lots todo. But one can already use it.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 6.10
 */
final class GDT_MimeType extends GDT_String
{
	public $max = 96;
	public bool $caseSensitive = true;
	public int $encoding = self::ASCII;
	
	public function defaultLabel() { return $this->label('file_type'); }

}
