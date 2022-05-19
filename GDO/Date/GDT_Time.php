<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_String;

class GDT_Time extends GDT_String
{
// 	public function defaultLabel() : self { return $this->label('date'); }
	
	public function gdoColumnDefine() : string
	{
		return "{$this->identifier()} TIME {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	public function renderForm() : string
	{
		return GDT_Template::php('Date', 'form/time.php', ['field'=>$this]);
	}
}
