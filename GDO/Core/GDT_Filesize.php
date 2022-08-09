<?php
namespace GDO\Core;

use GDO\Util\FileUtil;

/**
 * Display int as human readable filesize.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
final class GDT_Filesize extends GDT_UInt
{
	public function defaultLabel() : self { return $this->label('filesize'); }
	
	public function renderCell() : string
	{
		if ($size = $this->getValue())
		{
			return FileUtil::humanFilesize($size);
		}
		return '';
	}
	
	public function toValue($var = null)
	{
	    return $var === null ? null : (int) FileUtil::humanToBytes($var);
	}
	
}
