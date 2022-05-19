<?php
namespace GDO\File;

use GDO\Core\GDT_UInt;

/**
 * Display int as human readable filesize.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.1.0
 */
final class GDT_Filesize extends GDT_UInt
{
	public function defaultLabel() : self { return $this->label('filesize'); }
	
	public function renderCell() : string
	{
		return FileUtil::humanFilesize($this->getValue());
	}
	
	public function toValue($var)
	{
	    return (int) FileUtil::humanToBytes($var);
	}
	
}
