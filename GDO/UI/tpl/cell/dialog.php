<?phpuse GDO\UI\GDT_Dialog;use GDO\UI\GDT_Icon;/** @var $field GDT_Dialog * */$field->addClass('gdo-dialog');?><div <?=$field->htmlID()?> <?=$field->htmlAttributes()?>>    <dialog		<?php		if ($field->opened) : ?>open="open"<?php	endif; ?>>        <div class="gdo-dialog-inner">			<?php			if ($field->hasTitle()) : ?>                <h3><?=$field->renderTitle()?></h3>			<?php			endif; ?>            <div class="gdo-dialog-fields">				<?php				foreach ($field->fields as $gdt) : ?>					<?=$gdt->renderHTML()?>				<?php				endforeach; ?>            </div>			<?=$field->actions()->render()?>        </div>        <span class="gdo-dialog-close" onclick="GDO.closeDialog('<?=$field->id()?>', 'cancel')">      <?=GDT_Icon::iconS('close')?>    </span>    </dialog></div>