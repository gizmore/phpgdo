<?php
namespace GDO\Table\tpl;

use GDO\Core\GDT;
use GDO\Table\GDT_Order;

/** @var $order GDT_Order * */
/** @var $field GDT * */
?>
<div class="gdt-table-order">
    <label>
        <a rel="nofollow"
           class="<?=$order->htmlOrderClass($field)?>"
           href="<?=$order->nextHref($field)?>"><?=$order->htmlOrderIcon($field)?><?=$field->renderLabel()?></a>
    </label>
</div>
