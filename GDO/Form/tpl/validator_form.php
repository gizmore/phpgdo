<?php
namespace GDO\Form\tpl;

use GDO\Form\GDT_Validator;

/** @var $field GDT_Validator  * */
?>
<?php
if ($field->hasError()) : ?>
    <div class="gdt-container<?=$field->classError()?>">
		<?=$field->htmlError()?>
    </div>
<?php
endif; ?>
