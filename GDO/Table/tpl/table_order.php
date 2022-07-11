<?php
namespace GDO\Table\tpl;
/** @var $order \GDO\Table\GDT_Order **/
/** @var $field \GDO\Core\GDT **/
?>
<div class="gdt-table-order">
 <label>
  <a rel="nofollow"
     class="<?=$order->htmlOrderClass($field)?>"
     href="<?=$order->nextHref($field)?>"><?=$order->htmlOrderIcon($field)?> <?=$field->renderLabel()?></a>
  </label>
</div>
