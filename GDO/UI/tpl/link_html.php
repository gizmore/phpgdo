<?php
namespace GDO\UI\tpl;

use GDO\UI\GDT_Link;

/** @var $field GDT_Link * */
?>
<span class="<?=$field->htmlClass()?>"><a
<?=$field->htmlDisabled()?>
		<?=$field->htmlAttributes()?>
		<?=$field->htmlTarget()?>
		<?=$field->htmlHREF()?>
		<?=$field->htmlRelation()?>><?=$field->htmlIcon()?>
		<?php
		if (isset($field->href)) : ?>
			<?php
			if ($field->hasText()) : ?>
				<?=$field->renderText()?>
			<?php
			elseif (!$field->textNone) : ?>
				<?=html($field->href)?>
			<?php
			endif; ?>
		<?php
		else : ?>
			<?=t('not_specified')?>
		<?php
		endif; ?>
</a></span>
