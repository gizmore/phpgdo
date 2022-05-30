<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT;
use GDO\Table\GDT_Table;
/** @var $field GDT **/
/** @var $table GDT_Table **/
$lbl = $field->renderLabel();
$order = $table->order;

foreach (explode(',', $order->getVar()) as $o)
{
	if ($o == $field->getName())
	{
		
	}
}

// $input = $table->getHeaderField($name)