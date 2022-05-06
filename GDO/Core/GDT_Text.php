<?php
namespace GDO\Core;

/**
 * The text gdoType exceeds the varchar (GDT_String).
 * It is displayed in a textarea like form field.
 * The cell rendering should be dottet.
 * 
 * @author gizmore
 * @see GDT_String
 *
 */
class GDT_Text extends GDT_String
{
	public function defaultLabel() { return $this->label('message'); }
	
	public $max = 65535;
	
	public function gdoColumnDefine() : string
	{
		return "{$this->identifier()} {$this->gdoColumnDefineB()}";
	}
	
	protected function gdoColumnDefineB() : string
	{
	    $collate = $this->gdoCollateDefine($this->caseSensitive);
	    return "TEXT({$this->max}) CHARSET {$this->gdoCharsetDefine()} {$collate}{$this->gdoNullDefine()}";
	}
	
	public function validate($value) : bool
	{
		return parent::validate($value) ? $this->validateNonNumeric($value) : false;
	}
	
	public function validateNonNumeric($value)
	{
		return is_numeric($value) ? $this->error('err_text_only_numeric') : true;
	}
	
}
