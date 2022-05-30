<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT;
use GDO\Table\GDT_Table;
use GDO\Util\Strings;
/** @var $field GDT **/
/** @var $table GDT_Table **/
$lbl = $field->renderLabel();
$order = $table->order;

foreach (explode(',', $order->getVar()) as $o)
{
	$key = Strings::substrTo($o, ' ', $o);
	if ($gdt = $table->getHeaderField($key))
	{
		if ($gdt->isOrderable())
		{
			if ($order->hasInput())
			{
				
			}
		}
	}
	
	if ($o == $field->getName())
	{
		
	}
}

// $input = $table->getHeaderField($name)