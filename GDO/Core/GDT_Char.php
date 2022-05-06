<?php
namespace GDO\Core;

/**
 * A GDT_Char is a fixed length CHAR value in the db.
 * 
 * @author gizmore
 * @version 7.0.0
 */
class GDT_Char extends GDT_String
{
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	
	public function length($size)
	{
		$this->min = $this->max = $size;
		return $this;
	}

	public function gdoColumnDefine() : string
	{
		$collate = $this->gdoCollateDefine($this->caseSensitive);
		return
		"{$this->identifier()} CHAR({$this->max}) CHARSET {$this->gdoCharsetDefine()} {$collate}" .
		$this->gdoNullDefine() . $this->gdoInitialDefine();
	}
}
