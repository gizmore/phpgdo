<?php
namespace GDO\File;

use GDO\Core\GDT_String;

/**
 * Mime Filetype widget.
 * Lots todo. But one can already use it.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.2
 */
final class GDT_MimeType extends GDT_String
{
	public int $encoding = self::ASCII;
	public int $max = 96;
	public bool $caseSensitive = true;
	
	public function defaultLabel() : self { return $this->label('file_type'); }

}
