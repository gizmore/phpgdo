<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_String;

class GDT_Time extends GDT_String
{
	
	public function renderForm() : string
	{
		return GDT_Template::php('Date', 'form/time.php', ['field'=>$this]);
	}
	
}
