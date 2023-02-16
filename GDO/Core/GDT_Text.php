<?php
namespace GDO\Core;

use GDO\DBMS\Module_DBMS;

/**
 * The GDT_Text exceeds the limits of a GDT_String.
 * It is **not** displayed as a textarea.
 * Use GDT_Message for a textarea.
 * The cell rendering in tables should be dottet.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.2
 * @see GDT_String
 * @see GDT_Message
 */
class GDT_Text extends GDT_String
{
	public int $max = 65535;
	
	public function defaultLabel() : self { return $this->label('message'); }
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		return parent::validate($value) ? $this->validateNonNumeric($value) : false;
	}
	
	public function validateNonNumeric($value) : bool
	{
		return is_numeric($value) ? $this->error('err_text_only_numeric') : true;
	}
	
}
