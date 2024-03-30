<?php
namespace GDO\Core;

use GDO\Net\GDT_Url;
use GDO\UI\GDT_Icon;

/**
 * A file table for directory index.
 *
 * @version 6.10.6
 * @since 6.10.5
 * @author gizmore
 */
final class GDO_DirectoryIndex extends GDO
{

	public function gdoColumns(): array
	{
		return [
//         	GDT_AutoInc::make('file_id'),
			GDT_DirectoryIndex::make('file_name'),
			GDT_Icon::make('file_icon'),
			GDT_String::make('file_type'),
			GDT_Filesize::make('file_size'),
		];
	}

	public function getFileName(): string
	{
		return $this->gdoVar('file_name');
	}

	public function href_file_name()
	{
		return rtrim(GDO_WEB_ROOT, '/') . $_REQUEST['_url'] . '/' . $this->getFileName();
	}

}
