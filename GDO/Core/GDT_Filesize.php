<?php
namespace GDO\Core;

use GDO\Util\FileUtil;

/**
 * Display int as human readable filesize.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 */
final class GDT_Filesize extends GDT_UInt
{
	public function defaultLabel(): static { return $this->label('filesize'); }
	
	public function renderHTML() : string
	{
		if ($size = $this->getValue())
		{
			return FileUtil::humanFilesize($size);
		}
		return GDT::EMPTY_STRING;
	}
	
	public function renderJSON()
	{
		return $this->getValue();
	}
	
	public function toValue($var = null)
	{
	    return $var === null ? null : (int) FileUtil::humanToBytes($var);
	}
	
}
