<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Icon;
use GDO\Core\GDT;
/** @var int $mode **/
/** @var \GDO\UI\GDT_Accordeon $field **/
?>
<div class="gdt-panel gdt-accordeon <?=$field->opened?'opened':'closed'?>">
 <div class="title collapse-bar"><?=GDT_Icon::iconS('plus')?><?=$field->renderTitle()?></div>
 <div class="title uncollapse-bar"><?=GDT_Icon::iconS('minus')?><?=$field->renderTitle()?></div>
 <div class="collapse-content">
<?php
// switch ($mode)
// {
// 	case GDT::RENDER_FORM: $method = 'renderForm'; break;
// 	default: $method = 'renderHTML'; break;
// }
?>
<?php foreach ($field->getFields() as $gdt) : ?>
<?=$gdt->render()?>
 <?php #call_user_func([$gdt, $method])?>
<?php endforeach; ?>
 </div>
</div>
