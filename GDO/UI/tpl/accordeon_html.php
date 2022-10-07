<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Icon;
/** @var int $mode **/
/** @var \GDO\UI\GDT_Accordeon $field **/
?>
<div class="gdt-panel gdt-accordeon <?=$field->opened?'opened':'closed'?>">
 <div class="title collapse-bar"><a<?=$field->htmlName()?>>&nbsp;</a><?=GDT_Icon::iconS('plus')?><?=$field->renderTitle()?></div>
 <div class="title uncollapse-bar"><?=GDT_Icon::iconS('minus')?><?=$field->renderTitle()?></div>
 <div class="collapse-content">
<?php foreach ($field->getFields() as $gdt) : ?>
<?=$gdt->render()?>
<?php endforeach; ?>
 </div>
</div>
