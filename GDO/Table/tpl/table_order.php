<?php
namespace GDO\Table\tpl;

use GDO\Core\GDT;
use GDO\Table\GDT_Order;
use GDO\UI\GDT_Icon;

/** @var $order GDT_Order * */
/** @var $field GDT * */
?>
<div class="gdt-table-order">
    <label>
        <a rel="nofollow" aria-label="<?=$field->renderLabel()?> ascending"
           class="gdt-table-order-asc <?=$order->htmlOrderDirectionClass($field, GDT_Order::ASC)?>"
           href="<?=$order->hrefDirection($field, GDT_Order::ASC)?>"><?=GDT_Icon::iconS('arrow_up')?></a>
        <a rel="nofollow" aria-label="<?=$field->renderLabel()?> descending"
           class="gdt-table-order-desc <?=$order->htmlOrderDirectionClass($field, GDT_Order::DESC)?>"
           href="<?=$order->hrefDirection($field, GDT_Order::DESC)?>"><?=GDT_Icon::iconS('arrow_down')?></a>
        <?=$field->renderLabel()?>
    </label>
</div>
