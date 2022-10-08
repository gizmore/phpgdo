<?php
namespace GDO\Table\tpl;
use GDO\Form\GDT_Form;
/** @var $field \GDO\Table\GDT_Table **/
/** @var $form bool **/
$headers = $field->getHeaderFields();
$pm = isset($field->pagemenu) ? $field->pagemenu->renderHTML() : '';
$result = $field->getResult();
?>
<div class="gdt-table"<?=$field->htmlID()?>>
<?php if (!$form) : ?>
 <form method="get"<?=$field->htmlAction()?>>
<?=GDT_Form::htmlHiddenMoMe()?>
<?php endif; ?>
<?php if ($field->hasTitle()) : ?>
  <div class="gdo-table-caption">
    <h3><?=$field->renderTitle()?></h3>
  </div>
<?php endif; ?>
<?php if ($field->hasText() && $field->isFirstPage()) : ?>
  <div class="gdo-table-info"><p><?=$field->renderText()?></p></div>
<?php endif; ?>
  <table>
	<thead>
	  <?=$pm?>
	  <tr>
<?php foreach($headers as $gdt) : ?>
<?php if (!$gdt->isHidden()) : ?>
		<th class="<?=$gdt->htmlClass()?>">
<?php if ($field->isOrderable() && $gdt->isOrderable()) : ?>
			<?=$field->renderTableOrder($gdt)?>
<?php else : ?>
			<label><?=$gdt->renderTHead()?></label>
<?php endif; ?>
<?php if ($field->filtered) : ?>
			<?=$gdt->renderFilter($field->filter)?>
<?php endif; ?>
		</th>
<?php endif;?>
<?php endforeach; ?>
	  </tr>
	</thead>
	<tbody>
<?php $dummy = $result->table->cache->getDummy(); ?>
<?php while ($gdo = $result->fetchInto($dummy)) : ?>
	<tr data-gdo-id="<?=$gdo->getID()?>">
<?php foreach($headers as $gdt) :
	        if (!$gdt->isHidden()) :
	        $gdt->gdo($gdo); ?>
		<td class="<?=$gdt->htmlClass()?>"><?=$gdt->renderCell()?></td>
<?php endif; ?>
<?php endforeach; ?>
	</tr>
<?php endwhile; ?>
	</tbody>
<?php if (isset($field->footer)) : ?>
	<tfoot><?=$field->footer->renderTFoot()?></tfoot>
<?php endif; ?>
  </table>
  <input type="submit" class="n" />
<?php if ($actions = $field->getActions()) : ?>
<?=$actions->render()?>
<?php endif; ?>
<?php if (!$form) : ?>
 </form>
<?php endif; ?>
</div>
<?=$pm?>
